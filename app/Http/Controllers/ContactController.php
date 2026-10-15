<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+84|0)[0-9 .-]{8,16}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['required', Rule::in(['Tư vấn dịch vụ', 'Hỗ trợ đặt lịch', 'Góp ý', 'Nội dung khác'])],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'phone.regex' => 'Vui lòng nhập số điện thoại hợp lệ, bắt đầu bằng 0 hoặc +84.',
            'message.min' => 'Tin nhắn cần có ít nhất 10 ký tự.',
        ], [
            'name' => 'họ và tên', 'phone' => 'số điện thoại', 'email' => 'email',
            'subject' => 'chủ đề', 'message' => 'tin nhắn',
        ]);

        ContactMessage::create($data);

        return redirect()->route('public.contact')
            ->with('success', 'Garage đã nhận được tin nhắn của bạn.');
    }

    public function index()
    {
        return view('admin.contact.index', [
            'messages' => ContactMessage::latest()->paginate(15),
        ]);
    }
}
