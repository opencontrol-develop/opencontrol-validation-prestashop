# OpenControl-Validation-Prestashop

Herramienta de análisis de fraude de OpenControl para Prestashop

## Instalación

1. Descargar el .zip del siguiente repositorio https://github.com/opencontrol-develop/opencontrol-validation-prestashop.
2. En el panel de administración de Prestashop abrir el panel "Módulos" y hacer click en la opción "administrador de módulos".
3. Una vez hecho el paso anterior, dar click en el botón **Subir un modulo**.
4. La anterior acción abrirá un panel donde se le solicitará el archivo previamente descargado en el **paso 1**, dar click en **Seleccionar archivo** y localizar el archivo .zip.
5. Prestashop comenzará la instalación del plugin, una vez finalizada la instalación dar click en el botón **Configurar**.
   ![Add-new-plugin.png](https://img.openpay.mx/plugins/opencontrol_prestashop_add_new_plugin_01.gif)

## Actualización
En caso de ya contar con el módulo instalado y sea necesario actualizar, seguir los mismos pasos para la instalación, descargando el archivo .zip más actualizado.

## Administración
### 1. Configuración del módulo

Para configurar el módulo desde el panel de administrador de módulos de Prestashop busque el módulo: Opencontrol bajo la sección otros.
![Start_config.png](https://img.openpay.mx/plugins/opencontrol_prestashop_startConfig.png)

#### Características
- Análisis de transacciones mediante tarjetas crédito/débito.
- Selección de modo entre **Sandbox** y **Producción**.
  ![Configuration.png](https://img.openpay.mx/plugins/opencontrol_prestashop_configuration.png)
  

- Estatus personalizados en las órdenes analizadas por OpenControl.
    - **Denegado por OpenControl**
    - **Aprobado por OpenControl**
      ![Status.png](https://img.openpay.mx/plugins/opencontrol_prestashop_status.png)

#### Notas
*Para hacer uso de este módulo ponerse en contacto con soporte@openpay.mx*

**El funcionamiento del plugin puede estar condicionado por factores externos y/o la gestión de datos en cada método de pago, por lo cual es posible que requiera hacer cambios en la configuración o código de sus métodos de pago para obtener un correcto funcionamiento.**