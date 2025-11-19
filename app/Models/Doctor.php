<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo_medico',
        'numero_jvpm',
        'user_id',
        'nombres',
        'apellidos',
        'especialidad',
        'especialidades_secundarias',
        'telefono',
        'email',
        'direccion_consultorio',
        'horario_atencion',
        'estado',
        'company_id',
    ];

    /**
     * Accessor para obtener el nombre completo del médico
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    /**
     * Relación con el usuario (si el médico tiene cuenta de usuario)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación con citas médicas
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relación con consultas médicas
     */
    public function consultations()
    {
        return $this->hasMany(MedicalConsultation::class);
    }

    /**
     * Relación con órdenes de laboratorio
     */
    public function labOrders()
    {
        return $this->hasMany(LabOrder::class);
    }
}

