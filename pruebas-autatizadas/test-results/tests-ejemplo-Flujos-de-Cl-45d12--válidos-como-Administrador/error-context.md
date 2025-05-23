# Test info

- Name: Flujos de Cliente >> Registro de Productos >> CP-001: debe registrar un nuevo producto con datos válidos como Administrador
- Location: C:\AppServ\www\granos_app\pruebas-autatizadas\tests\ejemplo.spec.js:83:3

# Error details

```
Error: expect.toBeVisible: Error: strict mode violation: locator('text=Maíz blanco') resolved to 2 elements:
    1) <td>Maíz blanco</td> aka getByRole('cell', { name: 'Maíz blanco', exact: true })
    2) <td>Granos de maíz blanco de alta calidad.</td> aka getByRole('cell', { name: 'Granos de maíz blanco de alta' })

Call log:
  - expect.toBeVisible with timeout 5000ms
  - waiting for locator('text=Maíz blanco')

    at C:\AppServ\www\granos_app\pruebas-autatizadas\tests\ejemplo.spec.js:122:52
```

# Page snapshot

```yaml
- navigation:
  - link "GdO":
    - /url: ../admin/editar_perfil.php
  - list:
    - listitem:
      - link "Productos":
        - /url: ../admin/listar.php
    - listitem:
      - link "Vender":
        - /url: ../admin/vender.php
    - listitem:
      - link "Ventas":
        - /url: ../admin/ventas.php
    - listitem:
      - link "Administrar Usuarios":
        - /url: ../admin/listar_usuarios.php
    - listitem:
      - link "Administrar Almacenes":
        - /url: ../admin/listar_almacenes.php
    - listitem:
      - link "Cerrar Sesion":
        - /url: ../admin/logout.php
- heading "Productos" [level=1]
- link "Nuevo ":
  - /url: ./formulario.php
- table:
  - rowgroup:
    - row "ID Nombre Descripción Precio de compra Precio de venta Existencia Total Editar Eliminar":
      - cell "ID"
      - cell "Nombre"
      - cell "Descripción"
      - cell "Precio de compra"
      - cell "Precio de venta"
      - cell "Existencia Total"
      - cell "Editar"
      - cell "Eliminar"
  - rowgroup:
    - row "1 Arroz Blanco Arroz de grano largo 15.00 25.00 112 kg  ":
      - cell "1"
      - cell "Arroz Blanco"
      - cell "Arroz de grano largo"
      - cell "15.00"
      - cell "25.00"
      - cell "112 kg"
      - cell "":
        - link "":
          - /url: ./editar.php?id=1
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=1
    - row "2 Frijol Negro Frijol de la mejor calidad 12.50 65.00 135 kg  ":
      - cell "2"
      - cell "Frijol Negro"
      - cell "Frijol de la mejor calidad"
      - cell "12.50"
      - cell "65.00"
      - cell "135 kg"
      - cell "":
        - link "":
          - /url: ./editar.php?id=2
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=2
    - row "4 arroz jazmin chino 15.36 36.58 126 kg  ":
      - cell "4"
      - cell "arroz"
      - cell "jazmin chino"
      - cell "15.36"
      - cell "36.58"
      - cell "126 kg"
      - cell "":
        - link "":
          - /url: ./editar.php?id=4
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=4
    - row "5 Frijol Negro frijol bayo 23.60 30.50 123 kg  ":
      - cell "5"
      - cell "Frijol Negro"
      - cell "frijol bayo"
      - cell "23.60"
      - cell "30.50"
      - cell "123 kg"
      - cell "":
        - link "":
          - /url: ./editar.php?id=5
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=5
    - row "7 cebada cebada 12.30 23.50 1500 kg  ":
      - cell "7"
      - cell "cebada"
      - cell "cebada"
      - cell "12.30"
      - cell "23.50"
      - cell "1500 kg"
      - cell "":
        - link "":
          - /url: ./editar.php?id=7
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=7
    - row "8 Frijol Negro 500 gr 30.00 30.00 123 50  ":
      - cell "8"
      - cell "Frijol Negro"
      - cell "500 gr"
      - cell "30.00"
      - cell "30.00"
      - cell "123 50"
      - cell "":
        - link "":
          - /url: ./editar.php?id=8
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=8
    - row "15 Maíz blanco Granos de maíz blanco de alta calidad. 300.00 470.00 100 kg  ":
      - cell "15"
      - cell "Maíz blanco"
      - cell "Granos de maíz blanco de alta calidad."
      - cell "300.00"
      - cell "470.00"
      - cell "100 kg"
      - cell "":
        - link "":
          - /url: ./editar.php?id=15
      - cell "":
        - link "":
          - /url: ./eliminar.php?id=15
```

# Test source

```ts
   22 |   await page.waitForLoadState('networkidle'); 
   23 |   await expect(page).toHaveURL(expectedPostLoginURL); // Espera la URL específica del rol
   24 |
   25 |   // 6. Verificar el título y un elemento de bienvenida de la página post-login
   26 |   await expect(page).toHaveTitle(expectedPostLoginTitle);
   27 |   if (expectedWelcomeElement) { // Solo si se provee un elemento de bienvenida
   28 |     await expect(page.locator(expectedWelcomeElement)).toBeVisible();
   29 |   }
   30 | }
   31 |
   32 |
   33 | // --- Pruebas para el usuario Administrador ---
   34 | test.describe('Flujos de Administrador', () => {
   35 |   const admin_user = 'admin@example.com'; 
   36 |   const admin_pass = 'admin';       
   37 |
   38 |   test('debe iniciar sesion como Administrador y ver la pagina de productos de admin', async ({ page }) => {
   39 |     const expectedTitle = 'Granos'; 
   40 |     const expectedURL = 'http://localhost/granos_app/admin/listar.php';
   41 |     const expectedElement = 'h1:has-text("Productos")'; // El h1 con texto "Productos"
   42 |
   43 |     await iniciarSesion(page, admin_user, admin_pass, expectedTitle, expectedURL, expectedElement);
   44 |   });
   45 | });
   46 |
   47 |
   48 | // --- Pruebas para el usuario Empleado ---
   49 | test.describe('Flujos de Empleado', () => {
   50 |   const empleado_user = 'prueba@gmail.com'; 
   51 |   const empleado_pass = 'prueba';       
   52 |
   53 |   test('debe iniciar sesion como Empleado y ver la pagina de productos de empleado', async ({ page }) => {
   54 |     const expectedTitle = 'Granos'; 
   55 |     const expectedURL = 'http://localhost/granos_app/empleado/listar.php';
   56 |     const expectedElement = 'h1:has-text("Productos")'; // El h1 con texto "Productos"
   57 |
   58 |     await iniciarSesion(page, empleado_user, empleado_pass, expectedTitle, expectedURL, expectedElement);
   59 |   });
   60 | });
   61 |
   62 |
   63 | // --- Pruebas para el usuario Cliente ---
   64 | test.describe('Flujos de Cliente', () => {
   65 |   const cliente_user = 'cliente1@email.com'; // ¡Credenciales del cliente!
   66 |   const cliente_pass = 'prueba';       // ¡Credenciales del cliente!
   67 |
   68 |   test('debe iniciar sesion como Cliente y ver el perfil de usuario', async ({ page }) => {
   69 |     const expectedTitle = 'Granos'; // Título de la página del cliente
   70 |     const expectedURL = 'http://localhost/granos_app/cliente/listar.php'; // URL de la página del cliente
   71 |     // Puedes verificar el h1 "Grano de Oro" o "Productos", o un elemento del menú como "comprar"
   72 |     const expectedElement = 'h1:has-text("Granos de Oro")'; // El h1 con texto "Grano de Oro"
   73 |     // Opcional: const expectedElement = 'text=comprar'; // El enlace "comprar" en el menú
   74 |
   75 |     await iniciarSesion(page, cliente_user, cliente_pass, expectedTitle, expectedURL, expectedElement);
   76 |   });
   77 |
   78 |   // ... (Código existente de Playwright, incluyendo la función iniciarSesion) ...
   79 |
   80 | // --- Nuevas pruebas para el Registro de Productos (CP-001) ---
   81 | test.describe('Registro de Productos', () => {
   82 |
   83 |   test('CP-001: debe registrar un nuevo producto con datos válidos como Administrador', async ({ page }) => {
   84 |     // Reutilizamos la función de inicio de sesión para el Administrador
   85 |     const admin_user = 'admin@example.com'; 
   86 |     const admin_pass = 'admin';       
   87 |     const adminExpectedTitle = 'Granos'; 
   88 |     const adminExpectedURL = 'http://localhost/granos_app/admin/listar.php';
   89 |     const adminExpectedElement = 'h1:has-text("Productos")'; 
   90 |
   91 |     await iniciarSesion(page, admin_user, admin_pass, adminExpectedTitle, adminExpectedURL, adminExpectedElement);
   92 |
   93 |     // 1. Navegar a la página de agregar producto
   94 |     // Asumiendo que el botón "Nuevo +" lleva a ./nuevo.php (la URL de tu formulario)
   95 |     // ... (código anterior) ...
   96 |
   97 |     // 1. Navegar a la página de agregar producto
   98 |     // Anteriormente: await page.click('button:has-text("Nuevo +")'); 
   99 |     // Ahora, si el ID es "nuevo":
  100 |     await page.click('#nuevo'); // <--- ¡CAMBIO AQUÍ!
  101 |     
  102 |     await page.waitForURL('**/formulario.php'); // Espera que la URL cambie a la página de nuevo producto
  103 |
  104 |     // 2. Llenar el formulario con los datos indicados [cite: 4]
  105 |     await page.fill('#nombre', 'Maíz blanco'); // Campo Nombre
  106 |     await page.fill('#descripcion', 'Granos de maíz blanco de alta calidad.'); // Campo Descripción (el CP no lo da, pero el PHP lo requiere)
  107 |     await page.fill('#precio_compra', '300.00'); // Precio de compra (el CP no lo da, pero el PHP lo requiere)
  108 |     await page.fill('#precio_venta', '470.00'); // Precio de venta (dato "Precio" del CP)
  109 |     await page.fill('#unidad_medida', 'kg'); // Unidad de medida (el CP no da, pero el PHP lo requiere. Puedes usar "kg" o "bultos")
  110 |
  111 |     // Campo Stock para la Ubicación "Bodega Al" (asumiendo que su ID es 1)
  112 |     // **IMPORTANTE: Debes asegurarte de que '1' sea el ID correcto para 'Bodega Al' en tu BD.**
  113 |     await page.fill('#stock_1', '100'); // Cantidad 100 para Bodega Al (dato "Cantidad" del CP)
  114 |
  115 |     // 3. Hacer clic en "Guardar"
  116 |     await page.click('input[type="submit"][value="Guardar"]'); 
  117 |     // O si prefieres: await page.click('text=Guardar');
  118 |
  119 |     // 4. Resultado esperado: El producto se guarda correctamente y aparece en el listado de productos. [cite: 4]
  120 |     // Espera que la página redirija de vuelta a listar.php y que el nuevo producto sea visible.
  121 |     await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente'); // URL con mensaje de éxito
> 122 |     await expect(page.locator('text=Maíz blanco')).toBeVisible(); // Verifica que el nombre del producto aparece en la tabla
      |                                                    ^ Error: expect.toBeVisible: Error: strict mode violation: locator('text=Maíz blanco') resolved to 2 elements:
  123 |     await expect(page.locator('text=470.00')).toBeVisible(); // Verifica el precio de venta en la tabla
  124 |     await expect(page.locator('text=100')).toBeVisible(); // Verifica la cantidad en la tabla (si se muestra global o para el almacén principal)
  125 |
  126 |     // Opcional: Podrías añadir un paso para limpiar (eliminar) el producto después de la prueba
  127 |     // para mantener un estado limpio para futuras ejecuciones, pero eso es más avanzado.
  128 |   });
  129 |
  130 |   // Si el rol de Empleado también puede agregar productos, puedes duplicar esta prueba y cambiar las credenciales:
  131 |   // test('CP-001: debe registrar un nuevo producto con datos válidos como Empleado', async ({ page }) => {
  132 |   //   const empleado_user = 'prueba@gmail.com';
  133 |   //   const empleado_pass = 'prueba';
  134 |   //   const empleadoExpectedTitle = 'Granos';
  135 |   //   const empleadoExpectedURL = 'http://localhost/granos_app/empleado/listar.php';
  136 |   //   const empleadoExpectedElement = 'h1:has-text("Productos")';
  137 |
  138 |   //   await iniciarSesion(page, empleado_user, empleado_pass, empleadoExpectedTitle, empleadoExpectedURL, empleadoExpectedElement);
  139 |
  140 |   //   // ... (Los mismos pasos de llenado de formulario y verificación) ...
  141 |   //   await page.click('button:has-text("Nuevo +")');
  142 |   //   await page.waitForURL('**/nuevo.php');
  143 |   //   await page.fill('#nombre', 'Maíz blanco (Empleado)');
  144 |   //   await page.fill('#descripcion', 'Descripción de maíz de empleado');
  145 |   //   await page.fill('#precio_compra', '250.00');
  146 |   //   await page.fill('#precio_venta', '400.00');
  147 |   //   await page.fill('#unidad_medida', 'kg');
  148 |   //   await page.fill('#stock_1', '50'); // Asumiendo Bodega Al también para empleado
  149 |   //   await page.click('input[type="submit"][value="Guardar"]');
  150 |   //   await page.waitForURL('**/listar.php?mensaje=Producto%20agregado%20correctamente');
  151 |   //   await expect(page.locator('text=Producto agregado correctamente')).toBeVisible();
  152 |   //   await expect(page.locator('text=Maíz blanco (Empleado)')).toBeVisible();
  153 |   // });
  154 |
  155 | });
  156 | });
```