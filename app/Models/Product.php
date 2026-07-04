<?php

namespace App\Models;

use Cloudinary\Transformation\Resize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'compare_price',
        'quantity',
        'image',
        'image_public_id',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Mutator: تنظيف السعر قبل الحفظ
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = str_replace(',', '', $value);
    }

    /**
     * استخراج public_id من رابط Cloudinary
     */
    private function extractPublicIdFromUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // مثال: https://res.cloudinary.com/dtrrqx2i0/image/upload/c_fill,h_300,w_300/image1?_a=BAAHWXDQ
        // public_id = image1
        
        // نمط للبحث عن public_id
        $pattern = '/\/upload\/(?:v\d+\/)?(?:[^\/]+\/)?([^\/?]+)(?:\.[a-zA-Z]+)?(?:\?.*)?$/';
        preg_match($pattern, $url, $matches);
        
        return $matches[1] ?? null;
    }

    // Accessor: الحصول على صورة مصغرة
    public function getThumbnailAttribute()
    {
        // 1. لو فيه public_id موجود
        if ($this->image_public_id) {
            return cloudinary()->image($this->image_public_id)
                ->resize(Resize::fill()->width(300)->height(300))
                ->toUrl();
        }

        // 2. لو الصورة موجودة لكن public_id مش موجود (استخدام الرابط المباشر)
        if ($this->image) {
            $publicId = $this->extractPublicIdFromUrl($this->image);
            
            if ($publicId) {
                return cloudinary()->image($publicId)
                    ->resize(Resize::fill()->width(300)->height(300))
                    ->toUrl();
            }
            
            // لو فشل استخراج public_id، استخدم الرابط الأصلي
            return $this->image;
        }

        // 3. صورة افتراضية لو مفيش صورة
        return asset('images/no-image.png');
    }

    /**
     * الحصول على صورة بحجم مخصص
     */
    public function getImageUrl($width = null, $height = null)
    {
        // لو مفيش صورة
        if (!$this->image && !$this->image_public_id) {
            return asset('images/no-image.png');
        }

        // تحديد public_id
        $publicId = $this->image_public_id ?? $this->extractPublicIdFromUrl($this->image);
        
        // لو مش لاقي public_id
        if (!$publicId) {
            return $this->image ?? asset('images/no-image.png');
        }

        // بناء الصورة
        $image = cloudinary()->image($publicId);
        
        if ($width && $height) {
            $image->resize(Resize::fill()->width($width)->height($height));
        } elseif ($width) {
            $image->resize(Resize::fill()->width($width));
        }
        
        return $image->toUrl();
    }

    /**
     * الحصول على صورة محسنة (WebP) مع تحجيم
     */
    public function getOptimizedImage($width = null, $height = null)
    {
        if (!$this->image && !$this->image_public_id) {
            return asset('images/no-image.png');
        }

        $publicId = $this->image_public_id ?? $this->extractPublicIdFromUrl($this->image);
        
        if (!$publicId) {
            return $this->image ?? asset('images/no-image.png');
        }

        $image = cloudinary()->image($publicId)
            ->format('webp'); // تحويل لـ WebP لتحسين الأداء
        
        if ($width && $height) {
            $image->resize(Resize::fill()->width($width)->height($height));
        } elseif ($width) {
            $image->resize(Resize::fill()->width($width));
        }
        
        return $image->toUrl();
    }
}