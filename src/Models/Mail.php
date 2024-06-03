<?php

namespace Exonos\Mailapi\Models;

use Exonos\Mailapi\MessageParser;
use Exonos\Mailapi\Traits\AppendQueryParameters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Mail extends Model
{
    use HasFactory;
    use AppendQueryParameters;

    protected $table = 'mail_api';
    const STATUS_POSTED = 'posted';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 20;

    protected $guarded = [];
    protected $casts = [
        'variables' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public static function processRecipientsData(Request $request)
    {
        $recipients = $request->to;
        $recipients = collect($recipients)->map(function ($recipient) use ($request) {
            $recipient['variables'] = null;

            if ($request->variables) {
                $found = collect($request->variables)->first(function ($var) use ($recipient) {
                    return $recipient['email'] === $var['email'];
                });
                if ($found) {
                    $recipient['variables'] = $found['substitutions'];
                }
            }

            $recipient['subject'] = self::getSubject($request->subject, $recipient['variables']);
            $recipient['text'] = self::getText($request->text, $recipient['variables']);
            $recipient['html'] = self::getHtml($request->html, $recipient['variables']);

            return $recipient;
        });

        return $recipients->whereNotNull('variables');
    }


    private static function getText($text, $variables)
    {
        if ($text) {
            $variables = self::getVariables($variables);
            return MessageParser::substituteValues($text, $variables);
        }

        return null;
    }

    private static function getHtml($html, $variables)
    {
        if ($html) {
            $variables = self::getVariables($variables);
            return MessageParser::substituteValues($html, $variables);
        }
        return null;
    }

    private static function getSubject($subject, $variables)
    {
        $variables = Mail::getVariables($variables);
        return MessageParser::substituteValues($subject, $variables);
    }

    private static function getVariables($variables): array
    {
        if (!$variables) {
            return [];
        }
        return $variables;
    }
}