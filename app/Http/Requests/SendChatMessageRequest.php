<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'message' => ['required_without:attachment', 'nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpeg,png,jpg,webp,pdf,doc,docx,txt'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without' => 'Please type a message or choose an attachment file to send.',
            'attachment.max' => 'File attachments must not exceed 10 MB in size.',
            'attachment.mimes' => 'Allowed file types: JPG, PNG, WEBP, PDF, DOC, DOCX, TXT.',
        ];
    }
}
