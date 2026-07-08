@props(['name', 'label', 'value' => null, 'type' => 'text', 'required' => false, 'placeholder' => null, 'disabled' => false])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">
        {{ $label }}
        @if ($required) <span class="text-red-500">*</span> @endif
    </label>
    @if ($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" rows="3"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            placeholder="{{ $placeholder }}"
            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:bg-slate-50 disabled:text-slate-500 @error($name) border-red-400 @enderror"
        >{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select name="{{ $name }}" id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:bg-slate-50 @error($name) border-red-400 @enderror"
        >{{ $slot }}</select>
    @elseif ($type === 'file')
        <input type="file" name="{{ $name }}" id="{{ $name }}"
            accept="image/*"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100 @error($name) border-red-400 @enderror">
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            placeholder="{{ $placeholder }}"
            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:bg-slate-50 disabled:text-slate-500 @error($name) border-red-400 @enderror">
    @endif
    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>