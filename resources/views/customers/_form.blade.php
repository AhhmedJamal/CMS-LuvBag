
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-0">

        {{-- Name --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                {{ __('customers.name') }}
            </label>

            <input
                type="text"
                name="name"
                required
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                placeholder="{{ __('customers.name') }}">
        </div>

        {{-- Email --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                {{ __('customers.email') }}
            </label>

            <input
                type="email"
                name="email"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                placeholder="example@gmail.com">
        </div>

        {{-- Phone --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                {{ __('customers.phone') }}
            </label>

            <input
                type="text"
                name="phone"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                placeholder="+20 100 000 0000">
        </div>

        {{-- Address --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                {{ __('customers.address') }}
            </label>

            <input
                type="text"
                name="address"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                placeholder="{{ __('customers.address') }}">
        </div>

        {{-- Notes --}}
        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium text-gray-700">
                {{ __('customers.notes') }}
            </label>

            <textarea
                name="notes"
                rows="4"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                placeholder="{{ __('customers.notes') }}"></textarea>
        </div>

    </div>

    <div class="flex justify-end gap-3 mt-3 mb-8">
        <button
            type="button"
            class="px-5 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
            {{ __('app.close') }}
        </button>

        <button
            type="submit"
            class="px-5 py-2.5 rounded-lg bg-primary text-white hover:bg-primary-dark transition">
            <i class="fas fa-save mr-2"></i>
            {{ __('app.save') }}
        </button>
    </div>
