<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('phone', 20);
            $table->enum('role', ['admin', 'customer'])->default('customer');
            $table->string('address', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('brand_id');
            $table->string('name', 150);
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity');
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->string('main_image', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->cascadeOnDelete();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->integer('quantity');
            $table->decimal('price_at_add', 10, 2);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('shipping_address', 255);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->dateTime('paid_at')->nullable();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            
        });

        Schema::create('lucky_time_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('discount_percentage', 5, 2);
            $table->enum('status', ['active', 'inactive', 'scheduled'])->default('scheduled');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lucky_time_sessions');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
    }
};