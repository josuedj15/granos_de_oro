<?php include_once "./diseño/encabezado.php" ?>

<div class="col-xs-12">
    <h1>Nuevo producto</h1>
    <form method="post" action="./nuevo.php">
        <label for="nombre">Nombre:</label>
        <input class="form-control" name="nombre" required type="text" id="nombre" placeholder="Escribe el nombre">

        <label for="descripcion">Descripción:</label>
        <textarea required id="descripcion" name="descripcion" cols="30" rows="5" class="form-control"></textarea>

        <label for="precio_compra">Precio de compra:</label>
        <input class="form-control" name="precio_compra" required type="number" id="precio_compra" placeholder="Precio de compra" step="0.01">

        <label for="precio_venta">Precio de venta:</label>
        <input class="form-control" name="precio_venta" required type="number" id="precio_venta" placeholder="Precio de venta" step="0.01">

        <label for="unidad_medida">unidad de medida:</label>
        <input class="form-control" name="unidad_medida" required type="text" id="unidad_medida" placeholder="tipo de medida ej: kg, toneladas, bolsas">

        <h3>Stock Inicial en Almacenes</h3>
        <?php
        include_once "../base/conexion.php";
        $sentencia_almacenes = $conexion->query("SELECT id, nombre, ubicacion FROM almacenes");
        $almacenes = $sentencia_almacenes->fetchAll(PDO::FETCH_OBJ);

        if (!empty($almacenes)):
            foreach ($almacenes as $almacen):
                ?>
                <div class="form-group">
                    <label for="stock_<?php echo $almacen->id; ?>"><?php echo htmlspecialchars($almacen->nombre); ?> (<?php echo htmlspecialchars($almacen->ubicacion ?? 'Sin ubicación'); ?>):</label>
                    <input type="number" class="form-control" id="stock_<?php echo $almacen->id; ?>" name="stock[<?php echo $almacen->id; ?>]" value="0" min="0">
                </div>
                <?php
            endforeach;
        else:
            echo "<p>No hay almacenes creados aún. El stock inicial se establecerá en 0.</p>";
        endif;
        ?>

        <br><br><input class="btn btn-info" type="submit" value="Guardar">
    </form>
</div>
<?php include_once "./diseño/pie.php" ?>