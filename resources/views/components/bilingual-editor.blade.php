@props(['field', 'label', 'value_en' => '', 'value_fr' => '', 'type' => 'text', 'required' => false])

<div class="mb-3">
    <label class="form-label">{{ $label }}</label>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">EN</span>
                @if($type === 'textarea')
                    <textarea
                        name="{{ $field }}_en"
                        class="form-control @error($field . '_en') is-invalid @enderror"
                        @if($required) required @endif
                        placeholder="English {{ strtolower($label) }}"
                    >{{ old($field . '_en', $value_en) }}</textarea>
                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $field }}_en"
                        class="form-control @error($field . '_en') is-invalid @enderror"
                        value="{{ old($field . '_en', $value_en) }}"
                        @if($required) required @endif
                        placeholder="English {{ strtolower($label) }}"
                    >
                @endif
                @error($field . '_en')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">FR</span>
                @if($type === 'textarea')
                    <textarea
                        name="{{ $field }}_fr"
                        class="form-control @error($field . '_fr') is-invalid @enderror"
                        @if($required) required @endif
                        placeholder="French {{ strtolower($label) }}"
                    >{{ old($field . '_fr', $value_fr) }}</textarea>
                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $field }}_fr"
                        class="form-control @error($field . '_fr') is-invalid @enderror"
                        value="{{ old($field . '_fr', $value_fr) }}"
                        @if($required) required @endif
                        placeholder="French {{ strtolower($label) }}"
                    >
                @endif
                @error($field . '_fr')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
