<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    /**
     * Get validation rules that apply to the request.
     */
    public function rules(): array
    {
        $section = $this->route('section') ?? $this->input('_section', 'hero');

        return match ($section) {
            'hero' => $this->heroRules(),
            'workflow' => $this->workflowRules(),
            'schedule' => $this->scheduleRules(),
            'partner' => $this->partnerRules(),
            default => [],
        };
    }

    protected function heroRules(): array
    {
        return [
            'hero_title_1' => 'sometimes|required|string|max:255',
            'hero_title_2' => 'sometimes|required|string|max:255',
            'hero_subtitle' => 'sometimes|required|string|max:1000',
            'hero_image_file' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function workflowRules(): array
    {
        return [
            'workflow_title' => 'sometimes|required|string|max:255',
            'workflow_description' => 'sometimes|required|string|max:1000',
            'workflow_step1_title' => 'sometimes|required|string|max:255',
            'workflow_step1_desc' => 'sometimes|required|string|max:1000',
            'workflow_step2_title' => 'sometimes|required|string|max:255',
            'workflow_step2_desc' => 'sometimes|required|string|max:1000',
            'workflow_step3_title' => 'sometimes|required|string|max:255',
            'workflow_step3_desc' => 'sometimes|required|string|max:1000',
        ];
    }

    protected function scheduleRules(): array
    {
        return [
            'schedule_description' => 'sometimes|required|string|max:1000',
            'schedule_days' => 'sometimes|required|string|max:255',
            'schedule_hours' => 'sometimes|required|string|max:255',
            'schedule_note' => 'sometimes|nullable|string|max:1000',
            'schedule_image_file' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function partnerRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ];
    }
}
