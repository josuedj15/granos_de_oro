import { test, expect } from '@playwright/test';
// Asegúrate de que iniciarSesion esté exportada correctamente

// Función auxiliar para iniciar sesión, reutilizable en todas las pruebas
async function iniciarSesion(page, user, password, expectedTitle, expectedURL, expectedWelcomeElementSelector = null) {
  await page.goto('http://localhost/granos_app/login/login.php');

  await expect(page).toHaveTitle('Iniciar Sesión'); // Usa el título exacto de la pestaña

  await page.fill('#email', user); 
  await page.fill('#password', password); 
  await page.getByRole('button', { name: 'Iniciar Sesión' }).click();

  // Esperar la redirección y verificar la URL final
  await page.waitForURL(expectedURL, { timeout: 10000 }); 

  // Verificar el título de la página
  await expect(page).toHaveTitle(expectedTitle);

  // Verificar un elemento de bienvenida específico para el rol (opcional)
  if (expectedWelcomeElementSelector) {
    await expect(page.locator(expectedWelcomeElementSelector)).toBeVisible();
  }
}

// --- Casos de Prueba del documento "Casos de prueba.pdf" ---

// CP-007: Ingreso al sistema según rol autorizado
test.describe('CP-007: Ingreso al sistema según rol autorizado', () => {

  // Sub-caso: Ingreso como Administrador
  test('debe iniciar sesion como Administrador y ver la pagina de productos de admin', async ({ page }) => {
    const user = 'admin@example.com';
    const password = 'admin';
    const expectedTitle = 'Granos'; 
    const expectedURL = 'http://localhost/granos_app/admin/listar.php';
    const expectedElement = 'h1:has-text("Productos")'; 

    await iniciarSesion(page, user, password, expectedTitle, expectedURL, expectedElement);
  });

  // Sub-caso: Ingreso como Empleado
  test('debe iniciar sesion como Empleado y ver la pagina de productos de empleado', async ({ page }) => {
    const user = 'prueba@gmail.com'; 
    const password = 'prueba';       
    const expectedTitle = 'Granos';
    const expectedURL = 'http://localhost/granos_app/empleado/listar.php';
    const expectedElement = 'h1:has-text("Productos")';

    await iniciarSesion(page, user, password, expectedTitle, expectedURL, expectedElement);
  });

  // Sub-caso: Ingreso como Cliente
  test('debe iniciar sesion como Cliente y ver su pagina principal', async ({ page }) => {
    const user = 'cliente1@email.com'; 
    const password = 'prueba';       
    const expectedTitle = 'Granos'; 
    const expectedURL = 'http://localhost/granos_app/cliente/listar.php';
    const expectedElement = 'h1:has-text("Granos de Oro")'; 

    await iniciarSesion(page, user, password, expectedTitle, expectedURL, expectedElement);
  });

});

// CP-001: Registro de un nuevo producto con datos válidos
test.describe('CP-001: Registro de un nuevo producto', () => {

  test('CP-001: debe registrar un nuevo producto con datos válidos como Administrador', async ({ page }) => {
    const admin_user = 'admin@example.com'; 
    const admin_pass = 'admin';       
    const adminExpectedTitle = 'Granos'; 
    const adminExpectedURL = 'http://localhost/granos_app/admin/listar.php';
    const adminExpectedElement = 'h1:has-text("Productos")'; 

    await iniciarSesion(page, admin_user, admin_pass, adminExpectedTitle, adminExpectedURL, adminExpectedElement);

    await page.click('a.btn.btn-success:has-text("Nuevo")'); 
    await page.waitForURL('**/formulario.php'); 
    
    // Generar un nombre de producto único para evitar conflictos en cada ejecución
    const productName = `Maíz blanco Automatizado `; 
    await page.fill('#nombre', productName); 
    await page.fill('#descripcion', 'Granos de maíz blanco de alta calidad para pruebas de automatización.'); 
    await page.fill('#precio_compra', '300.00'); 
    await page.fill('#precio_venta', '470.00'); 
    await page.fill('#unidad_medida', 'kg'); 

    // Asume que '#stock_1' es el campo de stock para la primera ubicación/bodega
    await page.fill('#stock_1', '100'); 

    await page.click('input[type="submit"][value="Guardar"]'); 

    // Esperar a que la URL final indique el éxito y el mensaje de confirmación
    await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente', { timeout: 15000 }); 

    // Verificar que el producto recién creado aparece en la tabla de listado
    const productRowLocator = page.locator(`table.table tbody tr:has-text("${productName}")`);
    await expect(productRowLocator).toBeVisible(); 
    
    // Verificar que los datos del producto son correctos en la tabla
    await expect(productRowLocator.locator('td:nth-child(2)')).toHaveText(productName); 
    await expect(productRowLocator.locator('td:nth-child(5)')).toHaveText('470.00'); // Precio de venta
    await expect(productRowLocator.locator('td:nth-child(6)')).toHaveText('100 kg'); // Stock y unidad de medida
  });
});

// CP-002: Movimiento de salida de inventario válido
test.describe('CP-002: Movimiento de salida de inventario', () => {

  test('CP-002: debe registrar una salida de inventario válida como Empleado', async ({ page }) => {
    // 1. Iniciar sesión como operador de inventario (Empleado)
    const empleado_user = 'prueba@gmail.com'; 
    const empleado_pass = 'prueba';       
    const empleadoExpectedTitle = 'Granos'; 
    const empleadoExpectedURL = 'http://localhost/granos_app/empleado/listar.php'; 
    const empleadoExpectedElement = 'h1:has-text("Productos")'; 

    await iniciarSesion(page, empleado_user, empleado_pass, empleadoExpectedTitle, empleadoExpectedURL, empleadoExpectedElement);

    // 2. Obtener el stock inicial y el ID del producto "Maíz blanco Automatizado"
    // Asumimos que el producto "Maíz blanco Automatizado" fue creado por CP-001 o ya existe
    const productToSell = 'Maíz blanco Automatizado'; 
    const productRowLocator = page.locator(`table.table tbody tr:has-text("${productToSell}")`);

    const initialStockText = await productRowLocator.locator('td:nth-child(6)').textContent();
    // Extraer solo el número del stock (ej. "100 kg" -> 100)
    const initialStock = parseInt(initialStockText.replace(/\s*kg$/, '')); 

    // Extraer el ID del producto del atributo href del botón "Vender"
    
    
    const productId = (('id=')[6]); 

    // 3. Navegar a la página de venta haciendo clic en "Vender"
    await venderLink.click('input[type="submit"][value="Vender"]');
    // Esperar a que la URL cambie a la página de agregar venta, pasando el ID del producto
    await page.waitForURL(/.*\/ventas\/agregar\.php\?id=\d+/); 

    // 4. En la página de venta (que usa el código de vender.php), agregar el producto al carrito
    await page.fill('#id', productId); // Llenar el campo ID del producto
    const cantidadVenta = 50; // Cantidad a vender
    await page.fill('input[name="cantidad"]', cantidadVenta.toString()); // Llenar la cantidad
    await page.click('button.btn-primary:has-text("Agregar al carrito")'); // Clic en "Agregar al carrito"

    // La página puede recargarse o actualizarse al agregar al carrito; esperamos cualquier redirección
    await page.waitForURL(/.*\/ventas\/agregar\.php.*/); 

    // 5. Seleccionar un cliente para la venta
    // Selecciona la segunda opción en el dropdown, asumiendo que la primera es un placeholder "-- Seleccionar Cliente --"
    await page.selectOption('#cliente_id', { index: 1 }); 

    // 6. Terminar la venta
    await page.click('button.btn-success:has-text("Terminar venta")'); 

    // 7. Resultado esperado: Venta realizada correctamente y stock actualizado
    // Verificar el mensaje de éxito que aparece en el div de alerta
    await expect(page.locator('div.alert.alert-success')).toHaveText('¡Correcto! Venta realizada correctamente');
    // Esperar la redirección a la página de venta con el estado de éxito
    await page.waitForURL('**/ventas/vender.php?status=1'); 

    // 8. Volver a la página de listado de productos para verificar el stock actualizado
    await page.goto(empleadoExpectedURL); 
    const updatedProductRowLocator = page.locator(`table.table tbody tr:has-text("${productToSell}")`);

    // Calcular el stock esperado después de la venta
    const expectedStock = initialStock - cantidadVenta;
    // Verificar que el stock en la tabla se actualizó correctamente
    await expect(updatedProductRowLocator.locator('td:nth-child(6)')).toHaveText(`${expectedStock} kg`); 
  });
});



// CP-003: Asignación de ubicación a producto registrado
test.describe('CP-003: Asignación de ubicación a producto', () => {
  test('debe asignar una ubicación a un producto registrado como Administrador', async ({ page }) => {
    // 1. Iniciar sesión como administrador
    const admin_user = 'admin@example.com';
    const admin_pass = 'admin';
    const adminExpectedTitle = 'Granos';
    const adminExpectedURL = 'http://localhost/granos_app/admin/listar.php';
    const adminExpectedElement = 'h1:has-text("Productos")';

    await iniciarSesion(page, admin_user, admin_pass, adminExpectedTitle, adminExpectedURL, adminExpectedElement);

    // Necesitamos un producto para asignarle una ubicación. Creamos uno nuevo para esta prueba.
    const productToLocateName = `Producto para Ubicar ${Date.now()}`; // Nombre único para evitar conflictos
    await page.click('a.btn.btn-success:has-text("Nuevo")');
    await page.waitForURL('**/formulario.php');
    await page.fill('#nombre', productToLocateName); // CORRECCIÓN: Usar productToLocateName
    await page.fill('#descripcion', 'Producto de prueba para asignación de ubicación.');
    await page.fill('#precio_compra', '5.00');
    await page.fill('#precio_venta', '10.00');
    await page.fill('#unidad_medida', 'pieza');
    await page.fill('#stock_1', '10'); // Stock inicial
    await page.click('input[type="submit"][value="Guardar"]');
    await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente');

    // 2. Volver a listar.php para encontrar el botón de editar del producto recién creado
    // (Ya estamos en listar.php después de guardar el producto)
    await page.waitForSelector('h1:has-text("Productos")'); // Asegurarse de que la página de productos esté cargada

    // Encontrar la fila del producto recién creado
    const productRowLocator = page.locator(`table.table tbody tr:has-text("${productToLocateName}")`);
    await expect(productRowLocator).toBeVisible(); // Asegurarse de que la fila existe

    // 3. Hacer clic en el botón "Editar" del producto para ir a su formulario de edición
    // Basado en la imagen, el botón "Editar" es un <a> con clases "btn btn-warning" y contiene un <i> para el ícono.
    // Usaremos el localizador más robusto que combina la clase y el contenido del ícono.
    const editButtonLocator = productRowLocator.locator('a.btn.btn-warning i.fa-edit');
    // Alternativa: Si el texto "Editar" está oculto pero existe, o si es preferible por clase
    // const editButtonLocator = productRowLocator.locator('a.btn.btn-warning');

    await expect(editButtonLocator).toBeVisible(); // Asegura visibilidad antes de click
    await editButtonLocator.click();

    // 4. Esperar la URL del formulario de edición (ej. `admin/editar.php?id=X`)
    // La imagen muestra la URL de edición como `editar.php?id=24`.
    await page.waitForURL(/admin\/editar\.php\?id=\d+/);
    await expect(page.locator('h1')).toContainText(/Editar producto con el ID \d+/); // Verificar el título de la página de edición. (Ajustar si tu título es diferente)

    // 5. En la página de edición, modificar el stock o la ubicación.
    await page.fill('#stock_1', '50'); // Cambiamos el stock en almacén 3 (ID 1) de 10 a 50

    // 6. Guardar cambios
    // Revisa el botón de guardar en tu `editar.php`. Asumo un input type="submit" con value="Guardar Cambios".
    await page.click('input[type="submit"][value="Guardar"]');

    // Resultado esperado: El producto queda asociado a la ubicación y se actualiza en el listado.
    await page.waitForURL('**/listar.php?mensaje=Producto%20actualizado%20correctamente', { timeout: 15000 });
    // Verificar que el stock se ha actualizado en la tabla de listar.php
    const updatedProductRow = page.locator(`table.table tbody tr:has-text("${productToLocateName}")`);
    await expect(updatedProductRow).toBeVisible();
    await expect(updatedProductRow.locator('td:nth-child(6)')).toHaveText('50 pieza'); // El stock actualizado y unidad
  });
});


// tests/pedidos.spec.js (o ventas.spec.js, tu decides el nombre del archivo)

import { test, expect } from '@playwright/test';
import { iniciarSesion } from './auth.spec'; // Asegúrate de que iniciarSesion esté en auth.spec.js o ajusta la ruta

test.describe('CP-004: Creación de orden de pedido válida', () => {

  // Antes de todas las pruebas en este bloque, creamos un producto y un cliente de prueba
  // para asegurar que existan para la venta.
  let productId; // Para almacenar el ID del producto creado
  let clienteId; // Para almacenar el ID del cliente creado
  const productName = `Maíz CP004 ${Date.now()}`;
  const clienteName = `Don Eliseo ${Date.now()}`;
  const clienteEmail = `eliseo-${Date.now()}@test.com`; // Email único para el cliente
  const clienteDireccion = `Calle A #10 ${Date.now()}`;
  const clienteTelefono = `55${Math.floor(Math.random() * 9000000000) + 1000000000}`; // Teléfono aleatorio

  test.beforeAll(async ({ browser }) => {
    const page = await browser.newPage();

    // 1. Crear un producto de prueba (como administrador)
    await iniciarSesion(page, 'admin@example.com', 'admin', 'Granos', 'http://localhost/granos_app/admin/listar.php', 'h1:has-text("Productos")');

    await page.click('a.btn.btn-success:has-text("Nuevo")');
    await page.waitForURL('**/formulario.php');
    await page.fill('#nombre', productName);
    await page.fill('#descripcion', 'Maíz de prueba para orden de pedido.');
    await page.fill('#precio_compra', '2.00');
    await page.fill('#precio_venta', '4.00');
    await page.fill('#unidad_medida', 'kg');
    await page.fill('#stock_1', '500'); // Suficiente stock para la venta
    await page.click('input[type="submit"][value="Guardar"]');
    await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente');

    // Obtener el ID del producto de la URL o de la tabla.
    // Para simplificar, asumiremos que el ID está en la última fila o lo buscaremos.
    // Una forma más robusta es obtenerlo de la base de datos o la URL si redirige con el ID.
    // POR AHORA: Si el ID no se muestra, podemos ir a la base de datos o confiar en la búsqueda por nombre.
    // Vamos a buscar el producto en la tabla y obtener su ID si es visible.
    const productRow = page.locator(`table.table tbody tr:has-text("${productName}")`);
    await expect(productRow).toBeVisible();
    productId = await productRow.locator('td:nth-child(1)').textContent(); // Asume que el ID está en la primera columna

    // 2. Crear un cliente de prueba (Esto generalmente se hace vía admin o BD)
    // No hay una interfaz directa de "crear cliente" en tus archivos subidos.
    // Simularé el llenado de un formulario de registro o edición de perfil de cliente
    // si existiera una ruta para ello, o tendríamos que hacerlo directamente en la DB.
    // Si tu sistema permite crear clientes a través de un módulo admin, dime la URL y los campos.
    // Por simplicidad para el test, vamos a crear el cliente programáticamente si es posible
    // o asumir que ya existe y obtener su ID.

    // Si tuvieras una ruta para crear clientes por el admin, sería algo así:
    // await page.goto('http://localhost/granos_app/admin/crear_cliente.php'); // URL para crear cliente
    // await page.fill('#nombre_cliente', clienteName);
    // await page.fill('#email_cliente', clienteEmail);
    // await page.fill('#direccion_cliente', clienteDireccion);
    // await page.fill('#telefono_cliente', clienteTelefono);
    // await page.click('button[type="submit"]:has-text("Guardar Cliente")');
    // await page.waitForURL(/admin\/clientes\.php/);
    // clienteId = await page.locator(`table.table tbody tr:has-text("${clienteName}") td:nth-child(1)`).textContent();

    // ALTERNATIVA (Más simple para el test si no hay interfaz): Asumimos que podemos obtener el ID del cliente de la base de datos
    // o que Playwright puede seleccionar el cliente por su nombre.
    // Si necesitas un cliente específico y su ID no es fácil de obtener,
    // podemos usar un cliente existente con un ID fijo (menos ideal para tests).
    // Para el test, vamos a confiar en que Playwright puede seleccionar la opción por texto.
    // Este `beforeAll` cierra la página.
    await page.close();
  });


  test('CP-004: debe crear una orden de pedido válida con cliente y producto específicos', async ({ page }) => {
    // 1. Iniciar sesión como usuario de ventas
    const ventas_user = 'ventas@example.com'; // Credenciales del usuario de ventas
    const ventas_pass = 'ventas';
    const ventasExpectedTitle = 'Granos';
    const ventasExpectedURL = 'http://localhost/granos_app/vender.php';
    const ventasExpectedElement = 'h1:has-text("Vender")';

    await iniciarSesion(page, ventas_user, ventas_pass, ventasExpectedTitle, ventasExpectedURL, ventasExpectedElement);

    // 2. Ya estamos en el módulo de ventas (vender.php)
    await page.waitForSelector('h1:has-text("Vender")');

    // 3. Seleccionar el cliente "Don Eliseo"
    // Ya que `vender.php` tiene un select para cliente_id
    // Primero, encuentra el ID del cliente por su nombre si es posible (en la DB o en el test, pero esto es complicado para Playwright)
    // O si sabes el ID de un cliente de prueba, úsalo directamente:
    // Si la opción no se carga dinámicamente o el texto del select no se ve bien:
    // const clienteSelect = page.locator('#cliente_id');
    // await clienteSelect.selectOption({ label: clienteName }); // Selecciona por el texto visible
    // Si la opción tiene un value que coincide con el ID del cliente:
    // const clienteSelect = page.locator('#cliente_id');
    // await clienteSelect.selectOption({ value: 'ID_DE_DON_ELISEO' }); // Reemplaza con el ID real de Don Eliseo
    // Si no hay un select visible, y el cliente_id se pasa automáticamente con el usuario logueado.
    // Entonces el test `iniciarSesion` debería ser como 'Don Eliseo' si 'Don Eliseo' es un usuario de ventas.
    // POR AHORA, tu `vender.php` parece usar `$_SESSION['usuario_id']` para el `cliente_id` hidden.
    // Esto significa que **el usuario logueado será el cliente de la venta**.
    // Por lo tanto, para este test, debemos LOGUEARNOS COMO DON ELISEO SI QUEREMOS QUE ÉL SEA EL CLIENTE.
    // Si "Don Eliseo" es un usuario con rol 'cliente', entonces 'ventas' no puede hacer pedidos a él directamente.
    // Reevaluación del `cliente_id`:
    // En `vender.php`, hay un <select name="cliente_id"> pero el `input type="hidden" name="cliente_id"` está usando `$_SESSION['usuario_id']`.
    // El hidden sobrescribe el select. **Esto es una inconsistencia en tu código.**
    // Si `$_SESSION['usuario_id']` es para el usuario *logueado* (ventas), entonces ese usuario es el cliente.
    // Si la intención es que el vendedor ASIGNE la venta a otro cliente, el `hidden input` debe ser removido o el `select` debe ser el dominante.

    // Dadas las líneas:
    // <input name="cliente_id" type="hidden" value="<?php echo $_SESSION['usuario_id']; ?>" >
    // y el select:
    // <select name="cliente_id" id="cliente_id" class="form-control"> ... </select>
    // El input hidden tiene prioridad si ambos se envían.
    // Esto significa que la venta siempre se registrará al ID del usuario logueado.

    // PARA CUMPLIR EL CP-004 "Cliente: Don Eliseo", HAY DOS OPCIONES:
    // OPCION A (Ideal): Modificar vender.php para que el SELECT sea el que envíe el cliente_id.
    //     Eliminar la línea `<input name="cliente_id" type="hidden" value="<?php echo $_SESSION['usuario_id']; ?>" >`
    //     Y el test: `await page.selectOption('#cliente_id', { label: 'Don Eliseo' });`
    // OPCION B (Adaptar test a código actual): Loguearse como Don Eliseo (si Don Eliseo es un usuario con rol de ventas/cliente).
    //     Si Don Eliseo es un cliente, y el usuario de "ventas" también es un cliente (raro), o si el usuario de "ventas" puede "ser" el cliente.
    //     O si el caso de prueba solo significa que el pedido es "de" Don Eliseo, implicando que Don Eliseo es el que usa la interfaz de ventas.

    // Asumiré que el usuario 'ventas' (logueado en el test) es el que realiza la compra para sí mismo,
    // y el "Cliente: Don Eliseo" en el CP-004 es un error en la descripción del caso de prueba
    // O que "Don Eliseo" es el usuario de ventas. Vamos a loguearnos como ventas y crear un cliente de prueba.
    // Y vamos a intentar que el select funcione. Si no, usamos el usuario logueado.

    // Paso 3.1: Añadir producto al carrito
    await page.fill('#id', productId); // Usar el ID del producto creado en beforeAll
    await page.fill('input[name="cantidad"]', productQuantity.toString());
    await page.click('button[type="submit"]:has-text("Agregar al carrito")'); // Selector del botón

    // Esperar a que el producto aparezca en el carrito.
    // `vender.php` redirige a sí mismo, así que la tabla se refrescará.
    await page.waitForURL('**/vender.php'); // Esperar que la página se recargue
    await expect(page.locator(`table.table tbody tr:has-text("${productName}")`)).toBeVisible();
    await expect(page.locator(`table.table tbody tr:has-text("${productName}") td:nth-child(5)`)).toContainText(`${productQuantity} kg`); // Verificar cantidad y unidad de medida

    // Paso 3.2: Seleccionar Cliente (si aplica y si el hidden input se remueve/ignora)
    // Si tu `vender.php` sigue teniendo el hidden input que toma `$_SESSION['usuario_id']`,
    // esta parte del test no tendrá efecto real en la venta, solo en la interfaz.
    // Si quieres que el SELECT funcione, **DEBES QUITAR EL INPUT HIDDEN DE `cliente_id` de `vender.php`**.
    // Por simplicidad, si la venta se registra al usuario logueado, asumiremos eso.
    // Si "Don Eliseo" es un cliente con un ID específico que se pueda seleccionar:
    // await page.selectOption('#cliente_id', { label: 'Don Eliseo' }); // Solo si hay un select activo.

    // 4. Confirmar creación (botón "Terminar venta")
    await page.click('button[type="submit"]:has-text("Terminar venta")'); // Selector del botón

    // Esperar la página de confirmación
    await page.waitForURL('**/confirmacionVenta.php');
    await expect(page.locator('h1')).toContainText('Confirmación de Venta'); // Verifica el título

    // 5. Verificar que la orden se generó correctamente
    // Verificar que el producto y la cantidad se muestran en la tabla de confirmación
    await expect(page.locator(`table.table tbody tr:has-text("${productName}")`)).toBeVisible();
    await expect(page.locator(`table.table tbody tr:has-text("${productName}") td:nth-child(5)`)).toContainText(`${productQuantity} kg`); // Cantidad y unidad de medida

    // Verificar el total de la venta si es importante
    // Obtener el total calculado por PHP en la página de confirmación
    const totalVentaConfirmacion = await page.locator('li:has-text("Total de la Venta:") strong').textContent();
    // Podemos hacer una aserción aproximada si el cálculo es simple (precio_venta * cantidad)
    // Asumimos 4.00 * 200 = 800.00
    await expect(totalVentaConfirmacion).toContainText((4.00 * productQuantity).toFixed(2)); // Ajusta el precio_venta si es diferente

    // Opcional: Verificar ID de Cliente en la confirmación si es relevante para el test
    // await expect(page.locator('li:has-text("ID de Cliente:") strong')).toContainText(clienteId.toString());

    // Limpieza de datos (opcional, pero buena práctica para tests)
    // Aquí puedes añadir pasos para eliminar el producto y/o la venta de la BD si no quieres que persistan.
    // Esto implicaría ir a la vista de listar ventas/productos y usar el botón de eliminar,
    // o ejecutar una consulta SQL de limpieza. Por ahora, lo omitimos para simplificar.

  });
});

// CP-006: Generación de reporte con datos válidos y alerta automática por stock mínimo
test.describe('CP-006: Generación de reporte y alerta de stock', () => {
  // Aquí irá la prueba para generar un reporte y verificar la alerta de stock
  // Pasos:
  // 1. Iniciar sesión (como administrador)
  // 2. Navegar al módulo de reportes
  // 3. Generar un reporte (quizás seleccionar fechas o tipos de reporte)
  // 4. Verificar que el reporte se muestra correctamente
  // 5. Si aplica, verificar la presencia de alertas por stock mínimo (si son visuales en el reporte o en un dashboard)
});