<?php include_once "./diseño/encabezado.php" ?>

<div class="col-xs-12">
    <h1>Nuevo producto</h1>
    <form method="post" action="./nuevo.php">
        <label for="codigo">Nombre:</label>
        <input class="form-control" name="nombre" required type="text" id="nombre" placeholder="Escribe el nombre">

        <label for="descripcion">Descripción:</label>
        <textarea required id="descripcion" name="descripcion" cols="30" rows="5" class="form-control"></textarea>

        <label for="precio_compra">Precio de compra:</label>
        <input class="form-control" name="precio_compra" required type="number" id="precio_compra" placeholder="Precio de compra" step="0.01">

        <label for="precio_venta">Precio de venta:</label>
        <input class="form-control" name="precio_venta" required type="number" id="precio_venta" placeholder="Precio de venta" step="0.01">

        <label for="stock">Existencia:</label>
        <input class="form-control" name="stock" required type="number" id="stock" placeholder="Cantidad o existencia" step="0.01">
        
        <label for="unidad_medida">unidad de medida:</label>
        <input class="form-control" name="unidad_medida" required type="text" id="unidad_medida" placeholder="tipo de medida ej: kg, toneladas, bolsas">
        
        <br><br><input class="btn btn-info" type="submit" value="Guardar">
    </form>
</div>
<?php include_once "./diseño/pie.php" ?>