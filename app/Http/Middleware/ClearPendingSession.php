<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp_code', 6);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('email');
            $table->index('otp_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_otps');
    }
};