<?php

namespace Exonos\Mailapi\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Client extends Model
{

    use HasUuids;
    protected $keyType = 'string';
    protected $table = 'client_mail_api';
    protected $fillable = [
      'name',
      'secret',
      'revoked'
    ];
}