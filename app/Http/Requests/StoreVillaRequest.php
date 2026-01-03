<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVillaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $slugRule = 'nullable|string|max:255|unique:villas,slug';

        // For updates, exclude the current villa from unique check
        if ($this->route('villa') && is_object($this->route('villa'))) {
            $slugRule .= ',' . $this->route('villa')->getKey();
        }

        return [
            'title_en' => 'required|string|max:255',
            'title_fr' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_fr' => 'required|string',
            'amenities_en' => 'nullable|string',
            'amenities_fr' => 'nullable|string',
            'rules_en' => 'nullable|string',
            'rules_fr' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_shoulder_season' => 'nullable|numeric|min:0',
            'price_high_season' => 'nullable|numeric|min:0',
            'price_peak_season' => 'nullable|numeric|min:0',
            'max_guests' => 'required|integer|min:1|max:20',
            'min_guests' => 'nullable|integer|min:1|max:20',
            'featured_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'gallery_alt_en.*' => 'nullable|string|max:255',
            'gallery_alt_fr.*' => 'nullable|string|max:255',
            'featured' => 'sometimes|boolean',
            'published' => 'sometimes|boolean',
            'slug' => $slugRule,
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title_en.required' => 'English title is required',
            'title_fr.required' => 'French title is required',
            'description_en.required' => 'English description is required',
            'description_fr.required' => 'French description is required',
            'price.required' => 'Price per night is required',
            'max_guests.required' => 'Maximum guests is required',
        ];
    }
}
