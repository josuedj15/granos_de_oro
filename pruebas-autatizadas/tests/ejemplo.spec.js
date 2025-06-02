import { test, expect } from '@playwright/test';

// Función auxiliar para iniciar sesión, reutilizable en todas las pruebas
async function iniciarSesion(page, email, password, expectedTitle, expectedURL, expectedWelcomeElementSelector = null) {
  await page.goto('http://localhost/granos_app/login.php');

  // Asegurarse de que el formulario de login es visible
  await expect(page.locator('h1:has-text("Iniciar sesión")')).toBeVisible();

  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');

  // Esperar la redirección y verificar la URL final
  await page.waitForURL(expectedURL, { timeout: 10000 }); // Aumentamos timeout por si acaso

  // Verificar el título de la página
  await expect(page).toHaveTitle(expectedTitle);

  // Verificar un elemento de bienvenida específico para el rol (opcional)
  if (expectedWelcomeElementSelector) {
    await expect(page.locator(expectedWelcomeElementSelector)).toBeVisible();
  }
}

// ... (Código anterior de ejemplo.spec.js, incluyendo la función iniciarSesion) ...

// --- Casos de Prueba del documento "Casos de prueba.pdf" ---

// CP-007: Ingreso al sistema según rol autorizado
test.describe('CP-007: Ingreso al sistema según rol autorizado', () => {

  // Sub-caso: Ingreso como Administrador
  test('debe iniciar sesion como Administrador y ver la pagina de productos de admin', async ({ page }) => {
    const user = 'admin@example.com';
    const password = 'admin';
    const expectedTitle = 'Granos'; // Título esperado de la página después del login
    const expectedURL = 'http://localhost/granos_app/admin/listar.php'; // URL a la que debería redirigir
    const expectedElement = 'h1:has-text("Productos")'; // Un elemento visible en la página de admin

    await iniciarSesion(page, email, password, expectedTitle, expectedURL, expectedElement);
  });

  // Sub-caso: Ingreso como Empleado
  test('debe iniciar sesion como Empleado y ver la pagina de productos de empleado', async ({ page }) => {
    const user = 'prueba@gmail.com'; // Credenciales de tu usuario empleado
    const password = 'prueba'; // Contraseña de tu usuario empleado
    const expectedTitle = 'Granos';
    const expectedURL = 'http://localhost/granos_app/empleado/listar.php';
    const expectedElement = 'h1:has-text("Productos")';

    await iniciarSesion(page, email, password, expectedTitle, expectedURL, expectedElement);
  });

  // Sub-caso: Ingreso como Cliente
  test('debe iniciar sesion como Cliente y ver su pagina principal', async ({ page }) => {
    const user = 'cliente1@email.com'; // Credenciales de tu usuario cliente
    const password = 'prueba'; // Contraseña de tu usuario cliente
    const expectedTitle = 'Granos';
    const expectedURL = 'http://localhost/granos_app/cliente/listar.php';
    // Según tu imagen_67a189.png, el cliente ve "Grano de Oro" en el encabezado.
    const expectedElement = 'h1:has-text("Grano de Oro")';

    await iniciarSesion(page, email, password, expectedTitle, expectedURL, expectedElement);
  });

});

// CP-001: Registro de un nuevo producto con datos válidos
test.describe('CP-001: Registro de un nuevo producto', () => {
  // Aquí irá la prueba para registrar un producto como Administrador
});

// CP-002: Movimiento de salida de inventario válido
test.describe('CP-002: Movimiento de salida de inventario', () => {
  // Aquí irá la prueba para registrar una venta como Empleado
});

// CP-003: Asignación de ubicación a producto registrado
test.describe('CP-003: Asignación de ubicación a producto', () => {
  // Aquí irá la prueba para asignar un producto a una ubicación
});

// CP-004: Creación de orden de pedido válida
test.describe('CP-004: Creación de orden de pedido', () => {
  // Aquí irá la prueba para crear una orden de pedido
});

// CP-006: Generación de reporte con datos válidos y alerta automática por stock mínimo
test.describe('CP-006: Generación de reporte y alerta de stock', () => {
  // Aquí irá la prueba para generar un reporte
});