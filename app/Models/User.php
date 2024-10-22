<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_banner',
        'documento',
        'email',
        'password',
        'nombre',
        'id_rol',
        'id_facultad',
        'programa',
        'ingreso_plataforma',
        'activo',
        'nombre_rol'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setDocumentoAttribute($value){
        /**Recibe el valor del campo documento delformulario de registro */
        /**se agrega al campo documento */
        $this->attributes['documento'] = $value;
        /** El valor del campo documento se usa como password, por ello se cifra con la funcion bcrypt y se almacena en el campo password*/
        $this->attributes['password'] = bcrypt($value);
    }

    /** metodo para crear un string del arreglo programa recibido desde el formulario de registro*/
    public function setProgramaAttribute($value)
    {
        /** se crea la variable $Programa para el string de los programas*/
        $Programas = '';
        /**se comprueba que el campo no este vacio*/
        if (isset($value)) :
            /** Se recorre el arreglo recibido, y se añade a la variable $Programa
             *  en cada iteracion, añadiendole el ; como separador
             */
            foreach ($value as $programa) :
                $Programas .= $programa . ";";
            endforeach;
            /**En el campo programa se añade el contenido de la variable $Programa */
            $this->attributes['programa'] = rtrim($Programas, ";");
        else:
            /** Si el valor recibido es vacio se pasa al campo este valor vacio */
            $this->attributes['programa'] = '';
        endif;
    }

}
