<?php

namespace App\Http\Controllers;

use App\Mail\FormFeedback;
use App\Models\Mail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail as FacadeMail;

class FormController extends Controller
{

    public function send(Request $request): Response|JsonResponse
    {
        $request->validate(
            [
                'theme'    => 'required', 'string',
                'username' => 'required', 'string',
                'email'    => 'required', 'email',
                'company'  => 'string',
                'phone'    => 'string',
                'text'     => 'string',
                'files'    => 'json',
            ]
        );

        $mail = new Mail();
        $mail->theme = $request->post('theme');
        $mail->from = $request->post('email');
        $mail->to = User::ADMIN_MAIL;
        $mail->username = $request->post('username');
        $mail->company = !empty($request->post('company')) ? $request->post('company') : null;
        $mail->phone = !empty($request->post('phone')) ? $request->post('phone') : null;
        $mail->text = !empty($request->post('text')) ? $request->post('text') : null;
        $mail->is_success_sent = false;
        $mail->attachments = !empty($request->post('files')) ? $request->post('files') : null;
        $mail->created_at = Carbon::now();
        $mail->updated_at = Carbon::now();
        $mail->save();

        try {
            FacadeMail::to($mail->to)->send(new FormFeedback($mail));

            $mail->is_success_sent = true;
            $mail->updated_at = Carbon::now();
            $mail->save();
            return response()->noContent(200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
