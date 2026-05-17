<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TRIGGER 1: ACTUALIZAR STOCK AL EDITAR TALLAS
        DB::statement('DROP TRIGGER IF EXISTS trg_stock_lona_update');
        DB::statement("
            CREATE TRIGGER trg_stock_lona_update
            AFTER UPDATE ON lona_tallas
            FOR EACH ROW
            BEGIN
                UPDATE variantes_producto
                SET stock = (
                    SELECT IFNULL(SUM(cantidad),0)
                    FROM lona_tallas
                    WHERE lona_id = NEW.lona_id
                )
                WHERE lona_id = NEW.lona_id;
            END
        ");

        // TRIGGER 2: ACTUALIZAR STOCK AL INSERTAR TALLAS
        DB::statement('DROP TRIGGER IF EXISTS trg_stock_lona_insert');
        DB::statement("
            CREATE TRIGGER trg_stock_lona_insert
            AFTER INSERT ON lona_tallas
            FOR EACH ROW
            BEGIN
                UPDATE variantes_producto
                SET stock = (
                    SELECT IFNULL(SUM(cantidad),0)
                    FROM lona_tallas
                    WHERE lona_id = NEW.lona_id
                )
                WHERE lona_id = NEW.lona_id;
            END
        ");

        // TRIGGER 3: DESCONTAR STOCK Y AUDITAR EN VENTAS
        DB::statement('DROP TRIGGER IF EXISTS trg_descuento_stock_venta');
        DB::statement("
            CREATE TRIGGER trg_descuento_stock_venta
            BEFORE INSERT ON orden_items
            FOR EACH ROW
            BEGIN
                DECLARE stock_actual INT;
                DECLARE lona_ref BIGINT;
                DECLARE talla_ref VARCHAR(10);

                SELECT lona_id, talla 
                INTO lona_ref, talla_ref
                FROM variantes_producto
                WHERE id = NEW.variante_id
                LIMIT 1;

                IF lona_ref IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Variante sin lona asociada';
                END IF;

                SELECT cantidad 
                INTO stock_actual
                FROM lona_tallas
                WHERE lona_id = lona_ref
                AND talla = talla_ref
                LIMIT 1;

                IF stock_actual IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'No existe stock para esa talla';
                END IF;

                IF stock_actual < NEW.cantidad THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Stock insuficiente';
                END IF;

                UPDATE lona_tallas
                SET cantidad = cantidad - NEW.cantidad
                WHERE lona_id = lona_ref
                AND talla = talla_ref;

                INSERT INTO historial_lonas (
                    lona_id,
                    orden_item_id,
                    accion,
                    talla,
                    cantidad_cambio,
                    cantidad_restante,
                    notas,
                    created_at,
                    updated_at
                )
                VALUES (
                    lona_ref,
                    NULL,
                    'descuento',
                    talla_ref,
                    (NEW.cantidad * -1),
                    stock_actual - NEW.cantidad,
                    CONCAT('Venta Orden #', NEW.orden_id, ' | Variante ', NEW.variante_id),
                    NOW(),
                    NOW()
                );
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_stock_lona_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_stock_lona_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_descuento_stock_venta');
    }
};
