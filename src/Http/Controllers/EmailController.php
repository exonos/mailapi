<?php

namespace Exonos\Mailapi\Http\Controllers;

use Exonos\Mailapi\Jobs\Mail as MailJob;
use Exonos\Mailapi\Models\Batch;
use Exonos\Mailapi\Models\Client;
use Exonos\Mailapi\Models\Mail;
use Exonos\Mailapi\Traits\Reply;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EmailController extends Controller
{
    use Reply;

    public function send(Request $request)
    {
        try {
            $secret = request()->header('secret');
            $client = Client::where('secret', '=', $secret);

            if (!$client->exists()) {
                return $this->error( __('unauthorized'), Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'from' => 'required|string',
                'to' => 'required|array|min:1',
                'to.*.name' => 'required|string',
                'to.*.email' => 'required|email',
                'subject' => 'required|string',
                'text' => [Rule::requiredIf(empty($request->html)), 'string'],
                'html' => [Rule::requiredIf(empty($request->text)), 'string'],
                'variables' => 'sometimes|required|array|min:1',
                'variables.*.email' => 'required|email',
                'variables.*.substitutions' => 'required|array|min:1',
                'variables.*.substitutions.*.var' => 'required|string',
                'variables.*.substitutions.*.value' => 'required|string',
                'attachments' => 'sometimes|required|array|min:1',
                'attachments.*.filename' => 'required|string',
                'attachments.*.content' => 'required|string'
            ]);

            $validator->after(function ($validator) use ($request) {
                if (is_array($request->attachments)) {
                    $message = 'the following attachment(s) are invalid ';
                    $thereWasAnError = false;
                    foreach ($request->attachments as $attachment) {
                        if (array_key_exists('filename', $attachment) && array_key_exists('content', $attachment)) {
                            $isValid = base64_encode(base64_decode($attachment['content'], true)) === $attachment['content'];
                            if (!$isValid) {
                                $thereWasAnError = true;
                                $message .= "{$attachment['filename']},";
                            }
                        }
                    }
                    if ($thereWasAnError) {
                        $validator->errors()->add('attachment', substr($message, 0, strlen($message) - 1));
                    }
                }
            });

            if ($validator->fails()) {
                return $this->errorWithData( message: __('Validation error'), data: $validator->errors()->toArray(), error: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $verificationCode = Str::random(20);
            $data = Batch::processMailRequestData($request, $client->first(), $verificationCode);
            $recipients = Mail::processRecipientsData($request);

            $batch = Batch::create($data);
            $batch->mails()->createMany($recipients);

            $batch->mails->each(function ($recipient) use ($batch) {
                MailJob::dispatch($batch, $recipient);
            });

            return $this->successWithData('Email agregado a la cola de procesamiento.', ['verification_code' => $verificationCode],Response::HTTP_ACCEPTED);
        } catch (Throwable $e) {
            return $this->success($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}