<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosInicialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void // <--- AQUÍ: Antes decía up(), ya quedó corregido a run()
    {
        // 1. USUARIO ADMINISTRADOR BASE
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Jader Perna Admin',
            'email' => 'admin@dgala.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. DOTACIONES (INVENTARIO PRINCIPAL)
        $dotacionMedica = DB::table('dotaciones')->insertGetId([
            'nombre' => 'Lote Antifluidos Médico',
            'descripcion' => 'Tela de alta resistencia para uniformes de salud',
            'min_lonas' => 2,
            'max_lonas' => 10,
            'lonas_activas' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. LONAS ASOCIADAS A LA DOTACIÓN
        $lonaId = DB::table('lonas')->insertGetId([
            'dotacion_id' => $dotacionMedica,
            'codigo' => 'LONA-MED-001',
            'tipo_producto' => 'Tela Quirúrgica',
            'categoria' => 'Salud',
            'color' => 'Azul Rey',
            'estado' => 'nuevo',
            'activa' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. STOCK POR TALLAS EN LAS LONAS (Aquí se van a disparar sus Triggers)
        DB::table('lona_tallas')->insert([
            ['lona_id' => $lonaId, 'talla' => 'S', 'cantidad' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['lona_id' => $lonaId, 'talla' => 'M', 'cantidad' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['lona_id' => $lonaId, 'talla' => 'L', 'cantidad' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. CATEGORÍAS DEL CATÁLOGO
        $catSalud = DB::table('categorias')->insertGetId([
            'nombre' => 'Línea Médica y Salud',
            'slug' => 'linea-medica-y-salud',
            'padre_id' => null,
            'orden' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subScrub = DB::table('categorias')->insertGetId([
            'nombre' => 'Scrubs Quirúrgicos',
            'slug' => 'scrubs-quirurgicos',
            'padre_id' => $catSalud,
            'orden' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. PRODUCTO PAPÁ
        $productoId = DB::table('productos')->insertGetId([
            'categoria_id' => $subScrub,
            'nombre' => 'Uniforme Médico Antifluidos Premium',
            'slug' => 'uniforme-medico-antifluidos-premium',
            'descripcion' => 'Uniforme cómodo, antifluidos de alta tecnología, ideal para largas jornadas médicas.',
            'precio_minorista' => 85000.00,
            'precio_mayorista' => 65000.00,
            'min_cantidad_mayorista' => 12,
            'publicado' => true,
            'permitir_sin_stock' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. VARIANTES DEL PRODUCTO
        DB::table('variantes_producto')->insert([
            [
                'producto_id' => $productoId,
                'lona_id' => $lonaId,
                'sku' => 'DG-MED-AZ-S',
                'color' => 'Azul Rey',
                'talla' => 'S',
                'stock' => 0,
                'precio_extra' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'producto_id' => $productoId,
                'lona_id' => $lonaId,
                'sku' => 'DG-MED-AZ-M',
                'color' => 'Azul Rey',
                'talla' => 'M',
                'stock' => 0,
                'precio_extra' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 8. IMAGEN DE MUESTRA PARA EL PRODUCTO
        DB::table('imagenes_producto')->insert([
            'producto_id' => $productoId,
            'variante_id' => null,
            'url' => 'https://dgala.com/storage/productos/scrub-azul.jpg',
            'es_portada' => true,
            'orden' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
