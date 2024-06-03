<?php

namespace Exonos\Mailapi\Models;

use Exonos\Mailapi\Traits\AppendQueryParameters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Batch extends Model
{
    use AppendQueryParameters;

    protected $table = 'mail_batches';
    protected $fillable = [
      'verification_code',
      'client_id',
      'text',
      'from',
      'html',
      'subject',
      'attachments',
      'status',
      'recipient_count',
      'pending_mail',
    ];

    const STATUS_UNCOMPLETE = 'uncomplete';
    const STATUS_COMPLETED = 'completed';

    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 20;

    protected $casts = [
        'attachments' => 'array',
    ];


    protected $guarded = [];

    public function mails()
    {
        return $this->hasMany(Mail::class);
    }

    public static function processMailRequestData(Request $request, Client $client, $code = null): array
    {
        $recipientCount = count($request->to);
        $data = [
            'verification_code' => $code,
            'client_id' => $client->id,
            'subject' => $request->subject,
            'from' => $request->from,
            'recipient_count' => $recipientCount,
            'pending_mail' => $recipientCount
        ];

        if ($request->text) {
            $data['text'] = $request->text;
        }

        if ($request->html) {
            $data['html'] = $request->html;
        }

        if ($request->attachments) {
            $data['attachments'] = $request->attachments;
        }
        return $data;
    }
}