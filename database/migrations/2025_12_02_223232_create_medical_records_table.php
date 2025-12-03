<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            // Clave foránea al paciente (esencial para el historial)
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            
            $table->date('record_date'); // Fecha de la visita/examen
            $table->string('doctor_name', 255)->nullable(); // Quién realizó el examen

            // --- CAMPOS GENERALES ---
            $table->text('consultation_reason')->nullable();
            $table->string('symptoms', 255)->nullable();
            $table->string('occupation', 255)->nullable();
            $table->string('dominant_hand', 50)->nullable();
            $table->string('dominant_eye', 50)->nullable();

            // --- EXAMEN OPTOMÉTRICO (Imagen 54f8e7.jpg y 54f8e0.jpg) ---
            
            // Agudeza Visual
            $table->string('visual_acuity_sc_od', 10)->nullable(); // Visión SC
            $table->string('visual_acuity_sc_oi', 10)->nullable(); // Visión SC
            $table->string('visual_acuity_dist_od', 10)->nullable(); // Visión Dist
            $table->string('visual_acuity_dist_oi', 10)->nullable(); // Visión Dist
            $table->string('visual_acuity_near_od', 10)->nullable(); // Visión Cerca
            $table->string('visual_acuity_near_oi', 10)->nullable(); // Visión Cerca
            
            // Retinoscopia
            $table->string('retinoscopy_dynamic_od', 50)->nullable();
            $table->string('retinoscopy_dynamic_oi', 50)->nullable();
            $table->string('retinoscopy_static_od', 50)->nullable();
            $table->string('retinoscopy_static_oi', 50)->nullable();

            // Lentes y Prescripción
            $table->string('last_rx_optic_date', 50)->nullable();
            $table->string('lens_type', 100)->nullable();
            $table->string('lens_color', 50)->nullable();
            $table->text('special_instructions')->nullable();
            $table->text('definitive_prescription_od')->nullable();
            $table->text('definitive_prescription_oi')->nullable();

            // Otros tests
            $table->string('test_stereopsis', 50)->nullable();
            $table->boolean('test_color')->default(false);
            $table->boolean('amsler_grid')->default(false);
            
            $table->text('optometry_observations')->nullable();
            
            // --- EXAMEN OFTALMOLÓGICO (Imagen 54f8c4.jpg y 54f61b.jpg) ---
            
            // Párpados y Anexos
            $table->text('eyelids_sup')->nullable();
            $table->text('eyelids_inf')->nullable();
            $table->text('eyelids_aberrations')->nullable();
            $table->text('eyelids_func_elev')->nullable();
            $table->text('eyelids_tonic_orbic')->nullable();
            $table->text('conjunctiva')->nullable();
            $table->text('sclera')->nullable();
            $table->text('lacrimal_vias')->nullable();
            $table->text('schirmer')->nullable();

            // Segmento Anterior
            $table->text('cornea_description')->nullable();
            $table->text('limbus')->nullable();
            $table->text('anterior_chamber_prof')->nullable();
            $table->text('iris_description')->nullable();
            $table->text('pupil_description')->nullable();
            $table->text('crystalline')->nullable();
            
            // Fondo de Ojo
            $table->text('fundus_description')->nullable();
            $table->text('disc_optic')->nullable();
            $table->text('vessels')->nullable();
            $table->text('retina')->nullable();
            $table->text('macula_fovea')->nullable();
            $table->text('viterous')->nullable();

            $table->text('ophthalmology_observations')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};