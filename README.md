# dw_4vGYMAPP
<em>:weight_lifting: :muscle: 4vGYM API REST :dumbbell:  :muscle:</em>


Esta API REST está diseñada para gestionar las actividades, monitores y tipos de actividad de 4vGYM, proporcionando operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y asegurando la integridad de los datos.

Recursos Disponibles
/activity-types
GET: Devuelve la lista de tipos de actividad. Cada tipo de actividad tiene un ID, nombre y el número de monitores necesarios para realizarla.
/monitors
GET: Devuelve la lista de monitores con información detallada, incluyendo ID, Nombre, Email, Teléfono y Foto.
POST: Permite crear nuevos monitores y devuelve la información del nuevo monitor.
PUT: Permite editar monitores existentes.
DELETE: Permite eliminar monitores.
/activities
GET: Devuelve la lista de actividades con detalles sobre los tipos de actividad, monitores incluidos y la fecha. Permite la búsqueda por fecha en formato dd-MM-yyyy.
POST: Permite crear nuevas actividades y devuelve la información de la nueva actividad. Se valida que la actividad tenga los monitores exigidos por el tipo de actividad. La fecha y la duración deben ajustarse a clases de 90 minutos que comienzan a las 09:00, 13:30 y 17:30.
PUT: Permite editar actividades existentes.
DELETE: Permite eliminar actividades.
Estructura de la Base de Datos
Se espera que la base de datos que soporte esta API contenga las siguientes tablas:

Monitores:

ID (clave primaria)
Nombre
Email
Teléfono
Foto
Tipos de Actividad:

ID (clave primaria)
Nombre
Número de Monitores Necesarios
Actividades:

ID (clave primaria)
Tipo de Actividad (clave foránea a Tipos de Actividad)
Fecha
Duración
Otros detalles de la actividad
Actividades-Monitores (N-M):

ID (clave primaria)
Actividad (clave foránea a Actividades)
Monitor (clave foránea a Monitores)
Validación de Campos
Se realiza validación de campos en las operaciones POST para garantizar la integridad de los datos.

¡Disfruta utilizando la API 4vGYM para gestionar las actividades y monitores de tu gimnasio!
