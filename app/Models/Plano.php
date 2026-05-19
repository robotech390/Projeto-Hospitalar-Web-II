<?php

class Plano extends Model{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'plano';
    public $timestamps = false;
    protected $fillable = ['descricao', 'id_tipo_cobranca', 'id_convenio'];
}
