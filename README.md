# Pieces Service - Gestión de Producción y Pesaje

Este microservicio se encarga de la lógica central del sistema: la gestión de proyectos, bloques estructurales, catálogo de piezas y el registro de fabricación con control de pesajes. Desarrollado con **Laravel 11**.

## 🛠️ Tecnologías Utilizadas

*   **Framework:** Laravel 11
*   **Base de Datos:** MySQL (Arquitectura multi-base de datos)
*   **ORM:** Eloquent
*   **API:** RESTful con versionado /v1
*   **Middleware:** Remote Authentication (Validación de Bearer Tokens contra Auth-Service)

## 🏗️ Arquitectura de Datos

El servicio maneja la siguiente jerarquía relacional:
**Proyecto** ➔ **Bloques** ➔ **Piezas** ➔ **Fabricación (Pesaje)**

## 🔧 Instalación y Configuración

1. **Clonar el repositorio:**
   ```bash
   git clone <url-de-tu-repositorio>
   cd pieces-service
