# INSTITUTO TECNOLÓGICO SANTA CRUZ

## Técnico Superior en Sistemas Informáticos

---

# Aplicación Web Interactiva para la Gestión de Planos Técnicos, Listas de Despiece y Guías de Ensamblaje de Muebles en Madera y Melamina

## "Mydo Mar"

---

**Postulante:**

| N | Nombre Completo               | Unidad Educativa                  |
| - | ----------------------------- | --------------------------------- |
| 1 | Mauricio Miguel Quispe Flores | Instituto Tecnológico Santa Cruz  |

**Tutor:** Lic. Luis Tola

**Santa Cruz – Bolivia**

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

## Índice

- [Introducción](#introducción)
- [1. Planteamiento del Problema](#1-planteamiento-del-problema)
  - [1.1. Diagnóstico y Justificación](#11-diagnóstico-y-justificación)
  - [1.2. Planteamiento y Formulación del Problema](#12-planteamiento-y-formulación-del-problema)
  - [1.3. Objetivos](#13-objetivos)
  - [1.4. Enfoque Metodológico](#14-enfoque-metodológico)
  - [1.5. Justificación](#15-justificación)
- [2. Marco Teórico Conceptual](#2-marco-teórico-conceptual)
- [Referencias](#referencias)

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

## Introducción

En Bolivia, el crecimiento sostenido del sector del diseño de interiores ha impulsado el uso de la madera y, en particular, de la melamina como materiales preferentes para la fabricación de mobiliario, debido a su versatilidad, costo accesible y facilidad de mecanizado. No obstante, este crecimiento convive con una brecha tecnológica considerable entre la etapa de creación del diseño técnico y su ejecución material por parte del usuario final: carpinteros y aficionados que desean construir sus propios muebles carecen, en su mayoría, de acceso a planos normalizados, listas de materiales precisas y guías de ensamblaje estructuradas.

Frente a esta problemática se plantea el proyecto "Mydo Mar", una solución digital orientada a centralizar diseños de autor en una única plataforma web, que permita el acceso ordenado a planos técnicos y facilite la construcción autónoma de muebles. La propuesta está concebida para operar simultáneamente como herramienta didáctica para usuarios sin experiencia previa y como recurso de optimización operativa para profesionales del rubro de la carpintería.

De manera concreta, la aplicación permitirá que los diseñadores publiquen planos técnicos junto con sus listas de despiece detalladas, mientras que los maestros carpinteros podrán emplear dicho contenido como base para la fabricación de muebles a medida, ajustados a los estándares y materiales disponibles en el mercado boliviano.

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

## 1. Planteamiento del Problema

### 1.1. Diagnóstico y Justificación

En el contexto local, los diseñadores de mobiliario carecen de plataformas especializadas que permitan compartir o elaborar planos de forma interactiva y normalizada. Como consecuencia, los usuarios suelen recurrir a redes sociales, donde la información disponible suele ser incompleta, de baja calidad gráfica o carente de rigor técnico.

Asimismo, los carpinteros con experiencia enfrentan barreras significativas para acceder a herramientas digitales gratuitas que faciliten la transición del diseño manual al digital. Existe una dificultad concreta para obtener información técnica normalizada —listas de corte, especificación de herrajes y secuencias lógicas de armado— adaptada a los estándares y materiales disponibles en el mercado boliviano.

Esta realidad genera los siguientes problemas concretos:

- **Problema 1:** Los maestros carpinteros no disponen de una herramienta digital que les permita gestionar solicitudes de muebles a medida del cliente, desde el diseño hasta la lista de materiales necesarios para la fabricación.
- **Problema 2:** Los usuarios finales —carpinteros aficionados y profesionales— no encuentran guías técnicas confiables y adaptadas a los materiales disponibles localmente.
- **Problema 3:** No existe un sistema digital que genere automáticamente listas de corte y especificaciones de herrajes a partir de un diseño de mueble.
- **Problema 4:** La falta de guías interactivas de ensamblaje incrementa el margen de error durante la construcción, generando pérdidas de material y tiempo.

La propuesta se apoya en tecnologías web modernas —HTML, CSS, JavaScript y un sistema de gestión de bases de datos relacional— que permiten estructurar de forma ordenada los diseños, los materiales y las guías interactivas, ofreciendo una experiencia accesible desde cualquier dispositivo con conexión a internet.

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

### 1.2. Planteamiento y Formulación del Problema

#### 1.2.1. Planteamiento del Problema

- Los diseñadores de muebles en Bolivia carecen de plataformas digitales especializadas donde puedan publicar sus planos técnicos de forma estructurada y con rigor profesional.
- Los usuarios que desean fabricar muebles de manera autónoma no tienen acceso a listas de corte precisas ni a guías de ensamblaje paso a paso adaptadas al mueble deseado.
- La ausencia de herramientas digitales accesibles genera desperdicio de material, errores de medición y frustración en el proceso de fabricación artesanal.
- El sector carpintero local no cuenta con una plataforma que centralice el conocimiento técnico y lo ponga al alcance de usuarios con distintos niveles de experiencia.

#### 1.2.2. Formulación del Problema

**Pregunta principal:**

¿De qué manera el desarrollo de una aplicación web especializada puede optimizar la distribución de planos técnicos y guías de armado, facilitando la fabricación eficiente de muebles en madera y melamina?

#### 1.2.3. Signo Positivo del Problema

El problema abordado posee valor estratégico para el sector carpintero boliviano, ya que su solución impacta directamente en la calidad de los productos fabricados, la reducción del desperdicio de material y la disponibilidad de información técnica. Los diseñadores que actualmente trabajan de forma aislada podrían beneficiarse de un canal profesional de distribución, mientras que los usuarios finales ganarían autonomía y confianza en la fabricación de sus propios muebles.

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

### 1.3. Objetivos

#### 1.3.1. Objetivo General

Desarrollar una aplicación web interactiva que permita la gestión de diseños de muebles, facilitando la visualización de planos técnicos, la generación automática de listas de materiales y el acceso a guías de construcción, con el fin de optimizar el armado de muebles en madera y melamina.

#### 1.3.2. Objetivos Específicos

- Diseñar una interfaz de usuario (UI) intuitiva que permita la navegación eficiente por categorías y niveles de complejidad de los muebles.
- Implementar un módulo de administración para la carga y estructuración de planos técnicos, asegurando que los datos de despiece sean precisos para el usuario final.
- Desarrollar una base de datos relacional para el almacenamiento y gestión de catálogos de materiales, dimensiones de herrajes y especificaciones técnicas.
- Establecer un sistema de guías interactivas paso a paso que oriente al usuario en la secuencia de armado, reduciendo el margen de error en la construcción.
- Implementar un módulo diferenciado para diseñadores, orientado a la carga y gestión de planos, y otro para maestros carpinteros, orientado a la consulta de guías y a la gestión de pedidos de muebles a medida del cliente.
- Desarrollar un sistema de categorización de muebles por tipo, material y nivel de dificultad que facilite la búsqueda y selección por parte de los usuarios.

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

### 1.4. Enfoque Metodológico

#### 1.4.1. Tipo y Diseño de la Investigación

El presente proyecto se enmarca dentro de la investigación aplicada, dado que su propósito no es generar conocimiento teórico de manera autónoma, sino emplear el conocimiento técnico y científico disponible para resolver una problemática concreta del sector carpintero boliviano mediante el desarrollo de un producto tecnológico funcional. El diseño de la investigación es de carácter no experimental, ya que no se manipulan variables, sino que se observan, describen y analizan las condiciones actuales del entorno de trabajo de diseñadores y carpinteros para fundamentar las decisiones de diseño del sistema.

En cuanto al método, se emplea el método descriptivo para documentar los requerimientos del sistema y el flujo de trabajo entre diseñadores y usuarios finales, y el método analítico para procesar la información obtenida del sector carpintero y determinar a partir de ella las especificaciones técnicas necesarias para el desarrollo de la plataforma.

#### 1.4.2. Técnicas e Instrumentos de Recolección de Información

- **Entrevistas:** dirigidas a diseñadores de muebles y carpinteros, con el fin de identificar variables críticas en los planos y las necesidades reales del sector.
- **Encuestas de usabilidad:** aplicadas a potenciales usuarios para validar la experiencia de usuario (UX) de los prototipos desarrollados.
- **Análisis de plataformas existentes:** estudio comparativo de las plataformas Flatma y PolyBoard, empleadas como referencia funcional para el diseño del sistema.

#### 1.4.3. Metodología de Desarrollo del Software

Para la construcción de la solución se adoptará el marco de trabajo SCRUM, organizando el desarrollo en iteraciones cortas o *sprints* de dos semanas, con el fin de asegurar entregas funcionales e incrementales. Este enfoque permite adaptarse a los cambios en los requerimientos y validar cada módulo —diseñadores, catálogo, guías de ensamblaje— antes de avanzar al siguiente, conforme a los principios descritos por Schwaber y Sutherland (2020).

El desarrollo del proyecto se organiza en cinco fases secuenciales que abarcan desde el diagnóstico inicial hasta la documentación final del producto.

*Tabla 1*

*Fases de Desarrollo del Proyecto*

| Fase              | Actividades                                                          | Duración estimada |
| ----------------- | -------------------------------------------------------------------- | ------------------ |
| 1. Diagnóstico    | Entrevistas, análisis de plataformas, definición de requerimientos   | 3 semanas          |
| 2. Diseño         | Arquitectura de BD, mockups UI, definición de módulos                | 2 semanas          |
| 3. Desarrollo     | Implementación de frontend, backend y base de datos                  | 8 semanas          |
| 4. Pruebas        | Pruebas funcionales, encuestas de usabilidad                         | 2 semanas          |
| 5. Documentación  | Documento técnico final y presentación                               | 3 semanas          |

*Nota.* Cronograma elaborado conforme a las fases de diagnóstico, diseño, desarrollo, pruebas y documentación establecidas para el presente proyecto.

#### 1.4.4. Herramientas y Tecnologías

- **Frontend:** HTML, CSS y JavaScript
- **Backend:** Laravel, *framework* de PHP
- **Base de datos:** PostgreSQL
- **Control de versiones:** Git

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

### 1.5. Justificación

#### 1.5.1. Justificación Práctica

En la actualidad, muchos carpinteros y usuarios que desean fabricar muebles propios recurren a videos en línea o a grupos en redes sociales, donde la información disponible suele ser fragmentada, incompleta y carente de normalización. Un plano mal interpretado o una lista de corte incorrecta puede significar la pérdida de metros de melamina, lo cual representa un costo significativo para el usuario promedio en Bolivia.

"Mydo Mar" responde precisamente a esta realidad concreta: centraliza el conocimiento técnico en una plataforma accesible, en la que cada diseño se acompaña de su lista de corte, su especificación de herrajes y una guía de ensamblaje paso a paso. De esta manera se elimina la necesidad de consultar múltiples fuentes dispersas y se reduce de forma considerable el margen de error en la fabricación.

#### 1.5.2. Justificación Tecnológica

El desarrollo de aplicaciones web ha democratizado el acceso a herramientas digitales complejas. Las tecnologías web modernas permiten construir plataformas robustas, escalables y multiplataforma sin que el usuario final necesite instalar software adicional. Esta característica resulta especialmente relevante en Bolivia, donde el acceso a computadoras de escritorio es, en el sector carpintero, más habitual que el acceso a dispositivos móviles de gama alta.

La combinación de una base de datos relacional con una interfaz web interactiva permite gestionar de manera eficiente los catálogos de diseños, las especificaciones técnicas y las guías de ensamblaje, garantizando que la información se mantenga siempre precisa y actualizada.

#### 1.5.3. Justificación Económica

Los maestros carpinteros que trabajan a pedido enfrentan de manera constante el desafío de calcular manualmente los materiales necesarios para cada mueble a medida, lo que consume tiempo y genera errores costosos. "Mydo Mar" permite que el maestro registre las medidas específicas solicitadas por el cliente y obtenga automáticamente la lista de corte y los materiales requeridos, optimizando así el presupuesto y reduciendo el desperdicio.

Adicionalmente, al contar con planos estructurados y guías de ensamblaje precisas, el maestro carpintero puede delegar partes del trabajo a sus ayudantes con menor riesgo de error, lo que se traduce en mayor productividad y en una reducción de los costos operativos del taller.

#### 1.5.4. Justificación Académica

Desde el punto de vista académico, este proyecto integra conocimientos correspondientes a múltiples áreas de la carrera: diseño de bases de datos, desarrollo web *frontend* y *backend*, diseño de interfaces de usuario (UI/UX) y gestión de proyectos mediante metodologías ágiles. No se trata de aplicar una sola tecnología de forma aislada, sino de articularlas para resolver un problema real del entorno local.

El desarrollo de "Mydo Mar" demuestra que es posible construir soluciones tecnológicas funcionales y de impacto real con las herramientas adquiridas durante la formación técnica, aportando valor tanto al campo académico como al entorno productivo y artesanal de Bolivia.

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

## 2. Marco Teórico Conceptual

### 2.1. Aplicaciones Web Interactivas

Una aplicación web interactiva es un sistema de software alojado en servidores web y accesible a través de navegadores de internet, que permite al usuario no solo consultar información, sino también interactuar con el sistema, modificar datos y recibir respuestas dinámicas en tiempo real. A diferencia de un sitio web estático, las aplicaciones web interactivas responden a las acciones del usuario de forma inmediata, sin necesidad de recargar la página completa (Flanagan, 2020).

En el contexto de "Mydo Mar", esta característica resulta fundamental: los diseñadores necesitan cargar, editar y organizar sus planos técnicos de forma dinámica, mientras que los usuarios finales deben poder navegar por catálogos, filtrar por categorías y acceder a guías interactivas paso a paso sin fricciones.

### 2.2. Gestión de Planos Técnicos Digitales

Un plano técnico es una representación gráfica normalizada que describe con precisión las dimensiones, los materiales y los componentes de un objeto a fabricar. En el sector de la carpintería, los planos técnicos incluyen vistas ortogonales, cotas de medida, especificaciones de materiales y listas de despiece, también llamadas listas de corte.

La digitalización de planos técnicos permite su distribución masiva, su actualización instantánea y su integración con sistemas de generación automática de listas de materiales. Según Giesecke et al. (2006), la normalización de planos técnicos es fundamental para garantizar que cualquier persona con conocimientos básicos pueda reproducir fielmente el objeto diseñado.

### 2.3. Melamina y Madera: Materiales de Construcción de Muebles

La melamina es un tablero de partículas de madera (MDF o aglomerado) recubierto por una lámina decorativa de resina melamínica. Su popularidad en Bolivia se debe a su costo accesible, a la variedad de colores y texturas disponibles, y a su facilidad de mecanizado. Una lista de corte precisa para melamina debe incluir las dimensiones exactas de cada panel, el tipo y el espesor del tablero, y las especificaciones de los cantos y los herrajes.

La madera maciza, por su parte, requiere especificaciones adicionales, como el tipo de especie, el grado de humedad y las uniones estructurales. En ambos casos, la precisión en las listas de despiece resulta determinante para el éxito de la fabricación.

### 2.4. Plataformas de Referencia: Flatma y PolyBoard

Flatma es una plataforma de diseño de muebles en línea que presenta una biblioteca de categorías —cocinas, armarios, camas, escritorios—, un espacio de diseño con ejes tridimensionales y un panel de parámetros con dimensiones en tiempo real. Estas características sirven de referencia para el módulo de visualización de planos de "Mydo Mar" (Flatma, 2024).

PolyBoard, por su parte, es un software especializado en el diseño de muebles en melamina y madera. Su vista de despiece muestra las líneas de corte, las dimensiones de cada panel y un panel de propiedades con las especificaciones del mueble —altura, anchura, profundidad y configuración de paneles—. Este nivel de detalle constituye el estándar que "Mydo Mar" busca replicar en su módulo de listas de corte (PolyBoard, 2024).

La Tabla 2 resume, a manera de análisis comparativo, los alcances técnicos de ambas plataformas frente a la propuesta de "Mydo Mar".

*Tabla 2*

*Comparación de Alcances Técnicos entre Flatma, PolyBoard y "Mydo Mar"*

| Característica                    | Flatma  | PolyBoard | Mydo Mar    |
| --------------------------------- | ------- | --------- | ----------- |
| Diseño 3D interactivo             | ✓       | ✓         | — (futuro)  |
| Lista de corte automática         | —       | ✓         | ✓           |
| Guías de ensamblaje paso a paso   | —       | —         | ✓           |
| Acceso web sin instalación        | ✓       | —         | ✓           |
| Adaptado al mercado boliviano      | —       | —         | ✓           |
| Módulo para diseñadores           | ✓       | —         | ✓           |
| Módulo para maestros carpinteros  | —       | —         | ✓           |
| Catálogo de herrajes locales      | —       | —         | ✓           |
| Gratuito                          | Parcial | —         | ✓           |

*Nota.* Elaboración propia a partir del análisis comparativo de las plataformas Flatma (2024) y PolyBoard (2024).

### 2.5. Bases de Datos Relacionales

Una base de datos relacional es un sistema de almacenamiento de información organizado en tablas interrelacionadas mediante claves primarias y foráneas. Codd (1970) definió el modelo relacional como el estándar para la organización eficiente de datos estructurados, permitiendo la ejecución de consultas complejas mediante el lenguaje SQL.

Para "Mydo Mar", la base de datos relacional almacenará los catálogos de diseños, las especificaciones de materiales (tipos de melamina, herrajes, tornillería), los usuarios (diseñadores y carpinteros) y las guías de ensamblaje. La integridad referencial garantizará que cada plano se encuentre correctamente vinculado a sus materiales y guías correspondientes.

### 2.6. Metodología SCRUM

SCRUM es un marco de trabajo ágil para el desarrollo de software que organiza el trabajo en ciclos cortos denominados *sprints*. Schwaber y Sutherland (2020) definen SCRUM como un marco de trabajo ligero que ayuda a las personas, equipos y organizaciones a generar valor mediante soluciones adaptativas para problemas complejos.

En el contexto del proyecto "Mydo Mar", SCRUM permite dividir el desarrollo en módulos funcionales independientes —módulo de diseñadores, módulo de catálogo, módulo de guías de ensamblaje—, validar cada uno con usuarios reales antes de avanzar, y adaptarse a los cambios en los requerimientos durante el proceso de desarrollo.

### 2.7. Experiencia de Usuario (UX) e Interfaz de Usuario (UI)

La experiencia de usuario (UX) engloba todos los aspectos de la interacción del usuario final con un producto digital, incluyendo la facilidad de uso, la eficiencia y la satisfacción general. La interfaz de usuario (UI), en cambio, se refiere específicamente al diseño visual y a los elementos de interacción que el usuario percibe y manipula.

Según Nielsen y Molich (1990), los principios de usabilidad son fundamentales para garantizar que una aplicación sea intuitiva y eficiente. Para "Mydo Mar", esto implica diseñar una navegación clara por categorías, botones de acción visibles y guías paso a paso con imágenes ilustrativas que reduzcan la curva de aprendizaje de los usuarios con escasa experiencia digital.

A ello se suman los estándares de accesibilidad web, como las Pautas de Accesibilidad para el Contenido Web (WCAG), que constituyen un referente para asegurar que la plataforma sea utilizable por personas con distintas capacidades y niveles de competencia digital (UDIT, 2026; UxDT, s. f.). Estos lineamientos se tomarán como guía complementaria durante el diseño de la interfaz de "Mydo Mar".

### 2.8. Arquitectura Cliente-Servidor

La arquitectura cliente-servidor es un modelo de diseño de software en el que las tareas se distribuyen entre servidores, que proveen recursos o servicios, y clientes, que los solicitan. En este proyecto, el navegador web del usuario actúa como cliente, enviando solicitudes al servidor donde se aloja la aplicación "Mydo Mar", el cual procesa las peticiones y devuelve los datos correspondientes —planos, guías y listas de materiales—.

### 2.9. Framework Laravel

Laravel es un *framework* de código abierto para el desarrollo de aplicaciones web en PHP que sigue el patrón de diseño Modelo-Vista-Controlador (MVC). Creado por Taylor Otwell, destaca por su sintaxis elegante y por herramientas integradas como Eloquent ORM, que permite interactuar con la base de datos sin escribir SQL directo, las migraciones de base de datos, que posibilitan el control de versiones del esquema, y un motor de plantillas y enrutamiento robusto (Otwell, 2024). En "Mydo Mar", Laravel actuará como el motor del *backend*, exponiendo servicios API y gestionando el control de accesos de manera segura y eficiente.

### 2.10. Sistema de Gestión de Bases de Datos PostgreSQL

PostgreSQL es un sistema de gestión de bases de datos relacionales orientado a objetos, de código abierto y altamente estable, que destaca por su cumplimiento de los estándares SQL y por su robustez en el manejo de grandes volúmenes de datos. Admite características avanzadas, como transacciones ACID complejas y soporte nativo para datos de tipo JSONB (PostgreSQL Global Development Group, 2024). En el proyecto, PostgreSQL será el motor encargado de almacenar de manera estructurada a los usuarios, las categorías de muebles, las listas de corte y los pasos de ensamblaje, garantizando la integridad referencial y un rendimiento óptimo del sistema.

---

*"Formación Técnica y Tecnológica de Nivel Superior"*

---

## Referencias

Codd, E. F. (1970). A relational model of data for large shared data banks. *Communications of the ACM, 13*(6), 377–387. https://doi.org/10.1145/362384.362685

Flanagan, D. (2020). *JavaScript: The definitive guide* (7.ª ed.). O'Reilly Media.

Flatma. (2024). *Flatma — Furniture design platform*. https://flatma.com

Giesecke, F. E., Mitchell, A., Spencer, H. C., Hill, I. L., Dygdon, J. T., Novak, J. E., & Lockhart, S. (2006). *Dibujo y comunicación gráfica* (4.ª ed.). Pearson Educación.

Ministerio de Educación del Estado Plurinacional de Bolivia. (2023). *Resolución Ministerial N.° 0487/2023: Reglamento de modalidades de graduación de institutos técnicos y tecnológicos*.

Nielsen, J., & Molich, R. (1990). Heuristic evaluation of user interfaces. *Proceedings of the SIGCHI Conference on Human Factors in Computing Systems*, 249–256.

Otwell, T. (2024). *Laravel documentation*. Laravel. https://laravel.com/docs

PolyBoard. (2024). *PolyBoard — Cabinet design software*. https://www.polyboard.eu

PostgreSQL Global Development Group. (2024). *PostgreSQL 16 documentation*. PostgreSQL. https://www.postgresql.org/docs/

Schwaber, K., & Sutherland, J. (2020). *The Scrum guide*. Scrum.org. https://scrumguides.org

UDIT. (2026). *Web accessibility (WCAG) for UX/UI designers: Practical checklist*.

UxDT. (s. f.). *Accessibility testing checklist*.

---

## Apéndices

### Apéndice A

*Análisis Funcional de la Plataforma Flatma*

Análisis de Flatma como base funcional para la propuesta Mydo Mar. La plataforma presenta una biblioteca de categorías de muebles (cocinas, armarios, camas, escritorios, entre otros), un espacio de diseño con ejes 3D y un panel de parámetros con dimensiones en tiempo real. Estas características sirven de referencia para el módulo de visualización de planos de Mydo Mar.

*[Insertar Figura A1: Interfaz principal de Flatma — biblioteca de muebles, entorno de diseño 3D y panel de parámetros.]*

### Apéndice B

*Análisis Técnico de la Plataforma PolyBoard — Vista de Plano de Despiece*

Análisis de la estructura visual de PolyBoard y su facilidad de uso en la generación de planos técnicos. La vista de despiece muestra las líneas de corte, las dimensiones de cada panel y el panel de propiedades con especificaciones del mueble —altura, anchura, profundidad y configuración de paneles—. Este nivel de detalle es el estándar que Mydo Mar busca replicar en su módulo de listas de corte.

*[Insertar Figura B1: PolyBoard — Vista técnica de despiece con líneas de corte y panel de propiedades.]*

### Apéndice C

*Modelado 3D y Despiece en PolyBoard*

Visualización tridimensional renderizada de un mueble en PolyBoard. Este modelo ilustra el nivel de detalle constructivo que Mydo Mar incorporará en sus guías de ensamblaje paso a paso para orientar al usuario durante la construcción.

*[Insertar Figura C1: PolyBoard — Renderizado 3D de referencia para las guías de construcción de Mydo Mar.]*

---

*"Formación Técnica y Tecnológica de Nivel Superior"*
