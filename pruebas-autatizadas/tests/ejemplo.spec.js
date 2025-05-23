// Importa los módulos necesarios de Playwright
const { test, expect } = require('@playwright/test');

// --- Función auxiliar para iniciar sesión ---
async function iniciarSesion(page, username, password, expectedPostLoginTitle, expectedPostLoginURL, expectedWelcomeElement) {
  // 1. Navega a la URL de tu página de inicio de sesión
  await page.goto('http://localhost/granos_app/login/login.php');

  // 2. Verifica que el título de la página de inicio de sesión sea "Iniciar Sesión"
  //    ¡Esta es la línea que debe estar corregida con la tilde o el match exacto!
  await expect(page).toHaveTitle('Iniciar Sesión'); // Usa el título exacto de la pestaña

  // 3. Rellenar campos de usuario y contraseña (Selectores confirmados de tu captura)
  await page.fill('#email', username); 
  await page.fill('#password', password); 

  // 4. Hacer clic en el botón "Iniciar Sesión" (Selector confirmado)
  await page.getByRole('button', { name: 'Iniciar Sesión' }).click();

  // 5. Esperar a que la página post-login cargue completamente.
  //    Esperamos que la red esté inactiva y que la URL cambie.
  await page.waitForLoadState('networkidle'); 
  await expect(page).toHaveURL(expectedPostLoginURL); // Espera la URL específica del rol

  // 6. Verificar el título y un elemento de bienvenida de la página post-login
  await expect(page).toHaveTitle(expectedPostLoginTitle);
  if (expectedWelcomeElement) { // Solo si se provee un elemento de bienvenida
    await expect(page.locator(expectedWelcomeElement)).toBeVisible();
  }
}


// --- Pruebas para el usuario Administrador ---
test.describe('Flujos de Administrador', () => {
  const admin_user = 'admin@example.com'; 
  const admin_pass = 'admin';       

  test('debe iniciar sesion como Administrador y ver la pagina de productos de admin', async ({ page }) => {
    const expectedTitle = 'Granos'; 
    const expectedURL = 'http://localhost/granos_app/admin/listar.php';
    const expectedElement = 'h1:has-text("Productos")'; // El h1 con texto "Productos"

    await iniciarSesion(page, admin_user, admin_pass, expectedTitle, expectedURL, expectedElement);
  });
});


// --- Pruebas para el usuario Empleado ---
test.describe('Flujos de Empleado', () => {
  const empleado_user = 'prueba@gmail.com'; 
  const empleado_pass = 'prueba';       

  test('debe iniciar sesion como Empleado y ver la pagina de productos de empleado', async ({ page }) => {
    const expectedTitle = 'Granos'; 
    const expectedURL = 'http://localhost/granos_app/empleado/listar.php';
    const expectedElement = 'h1:has-text("Productos")'; // El h1 con texto "Productos"

    await iniciarSesion(page, empleado_user, empleado_pass, expectedTitle, expectedURL, expectedElement);
  });
});


// --- Pruebas para el usuario Cliente ---
test.describe('Flujos de Cliente', () => {
  const cliente_user = 'cliente1@email.com'; // ¡Credenciales del cliente!
  const cliente_pass = 'prueba';       // ¡Credenciales del cliente!

  test('debe iniciar sesion como Cliente y ver el perfil de usuario', async ({ page }) => {
    const expectedTitle = 'Granos'; // Título de la página del cliente
    const expectedURL = 'http://localhost/granos_app/cliente/listar.php'; // URL de la página del cliente
    // Puedes verificar el h1 "Grano de Oro" o "Productos", o un elemento del menú como "comprar"
    const expectedElement = 'h1:has-text("Granos de Oro")'; // El h1 con texto "Grano de Oro"
    // Opcional: const expectedElement = 'text=comprar'; // El enlace "comprar" en el menú

    await iniciarSesion(page, cliente_user, cliente_pass, expectedTitle, expectedURL, expectedElement);
  });

  // ... (Código existente de Playwright, incluyendo la función iniciarSesion) ...

// --- Nuevas pruebas para el Registro de Productos (CP-001) ---
test.describe('Registro de Productos', () => {

  test('CP-001: debe registrar un nuevo producto con datos válidos como Administrador', async ({ page }) => {
    // Reutilizamos la función de inicio de sesión para el Administrador
    const admin_user = 'admin@example.com'; 
    const admin_pass = 'admin';       
    const adminExpectedTitle = 'Granos'; 
    const adminExpectedURL = 'http://localhost/granos_app/admin/listar.php';
    const adminExpectedElement = 'h1:has-text("Productos")'; 

    await iniciarSesion(page, admin_user, admin_pass, adminExpectedTitle, adminExpectedURL, adminExpectedElement);

    // 1. Navegar a la página de agregar producto
    // Asumiendo que el botón "Nuevo +" lleva a ./nuevo.php (la URL de tu formulario)
    // ... (código anterior) ...

    // 1. Navegar a la página de agregar producto
    // Anteriormente: await page.click('button:has-text("Nuevo +")'); 
    // Ahora, si el ID es "nuevo":
    await page.click('#nuevo'); // <--- ¡CAMBIO AQUÍ!
    
    await page.waitForURL('**/formulario.php'); // Espera que la URL cambie a la página de nuevo producto

    // 2. Llenar el formulario con los datos indicados [cite: 4]
    await page.fill('#nombre', 'Maíz blanco'); // Campo Nombre
    await page.fill('#descripcion', 'Granos de maíz blanco de alta calidad.'); // Campo Descripción (el CP no lo da, pero el PHP lo requiere)
    await page.fill('#precio_compra', '300.00'); // Precio de compra (el CP no lo da, pero el PHP lo requiere)
    await page.fill('#precio_venta', '470.00'); // Precio de venta (dato "Precio" del CP)
    await page.fill('#unidad_medida', 'kg'); // Unidad de medida (el CP no da, pero el PHP lo requiere. Puedes usar "kg" o "bultos")

    // Campo Stock para la Ubicación "Bodega Al" (asumiendo que su ID es 1)
    // **IMPORTANTE: Debes asegurarte de que '1' sea el ID correcto para 'Bodega Al' en tu BD.**
    await page.fill('#stock_1', '100'); // Cantidad 100 para Bodega Al (dato "Cantidad" del CP)

    // 3. Hacer clic en "Guardar"
    await page.click('input[type="submit"][value="Guardar"]'); 
    // O si prefieres: await page.click('text=Guardar');

    // 4. Resultado esperado: El producto se guarda correctamente y aparece en el listado de productos. [cite: 4]
    // Espera que la página redirija de vuelta a listar.php y que el nuevo producto sea visible.
    await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente'); // URL con mensaje de éxito
    await expect(page.locator('text=Maíz blanco')).toBeVisible(); // Verifica que el nombre del producto aparece en la tabla
    await expect(page.locator('text=470.00')).toBeVisible(); // Verifica el precio de venta en la tabla
    await expect(page.locator('text=100')).toBeVisible(); // Verifica la cantidad en la tabla (si se muestra global o para el almacén principal)

    // Opcional: Podrías añadir un paso para limpiar (eliminar) el producto después de la prueba
    // para mantener un estado limpio para futuras ejecuciones, pero eso es más avanzado.
  });

  // Si el rol de Empleado también puede agregar productos, puedes duplicar esta prueba y cambiar las credenciales:
  // test('CP-001: debe registrar un nuevo producto con datos válidos como Empleado', async ({ page }) => {
  //   const empleado_user = 'prueba@gmail.com';
  //   const empleado_pass = 'prueba';
  //   const empleadoExpectedTitle = 'Granos';
  //   const empleadoExpectedURL = 'http://localhost/granos_app/empleado/listar.php';
  //   const empleadoExpectedElement = 'h1:has-text("Productos")';

  //   await iniciarSesion(page, empleado_user, empleado_pass, empleadoExpectedTitle, empleadoExpectedURL, empleadoExpectedElement);

  //   // ... (Los mismos pasos de llenado de formulario y verificación) ...
  //   await page.click('button:has-text("Nuevo +")');
  //   await page.waitForURL('**/nuevo.php');
  //   await page.fill('#nombre', 'Maíz blanco (Empleado)');
  //   await page.fill('#descripcion', 'Descripción de maíz de empleado');
  //   await page.fill('#precio_compra', '250.00');
  //   await page.fill('#precio_venta', '400.00');
  //   await page.fill('#unidad_medida', 'kg');
  //   await page.fill('#stock_1', '50'); // Asumiendo Bodega Al también para empleado
  //   await page.click('input[type="submit"][value="Guardar"]');
  //   await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente');
  //   await expect(page.locator('text=Producto agregado correctamente')).toBeVisible();
  //   await expect(page.locator('text=Maíz blanco (Empleado)')).toBeVisible();
  // });

});
});