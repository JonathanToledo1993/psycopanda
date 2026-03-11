<?php
/**
 * REBUILD WONDERLIC TEST - 50 QUESTIONS
 * Este script elimina las preguntas actuales de la prueba Wonderlic
 * e inserta 50 nuevas preguntas con dificultad progresiva.
 */

require_once 'config/db.php';
require_once 'utils/Responder.php';

// Intentar encontrar el testId de Wonderlic
try {
    $stmt = $pdo->prepare("SELECT id FROM catalog_tests WHERE `key` LIKE '%wonderlic%' OR `name` LIKE '%wonderlic%' LIMIT 1");
    $stmt->execute();
    $test = $stmt->fetch();

    if (!$test) {
        die("No se encontró la prueba 'Wonderlic' en el catálogo. Por favor, asegúrese de que el nombre o el key contengan 'wonderlic'.");
    }

    $testId = $test['id'];
    echo "Identificada prueba Wonderlic con ID: $testId\n";

    // 1. Limpiar preguntas actuales
    $pdo->prepare("DELETE FROM catalog_questions WHERE testId = ?")->execute([$testId]);
    echo "Eliminadas preguntas anteriores.\n";

    // 2. Definir las 50 preguntas
    // Estructura: [type, question, options, correctAnswer]
    $questions = [
        // ==========================================
        // DIFICULTAD: MUY FÁCILES (1-10)
        // ==========================================
        ['MULTIPLE_CHOICE', '¿Cuál de estos números es el más pequeño?', ['12', '4', '8', '21'], '4'],
        ['MULTIPLE_CHOICE', 'Si un paquete de galletas cuesta $2 y compras 3, ¿cuánto pagas?', ['$4', '$5', '$6', '$8'], '$6'],
        ['MULTIPLE_CHOICE', 'Completa la serie: 2, 4, 6, 8, ...', ['9', '10', '11', '12'], '10'],
        ['MULTIPLE_CHOICE', '¿Cuál es el antónimo (opuesto) de "Día"?', ['Luz', 'Mañana', 'Noche', 'Tarde'], 'Noche'],
        ['MULTIPLE_CHOICE', 'TRISTEZA es a LLANTO como ALEGRÍA es a:', ['Risa', 'Enojo', 'Sueño', 'Hambre'], 'Risa'],
        ['MULTIPLE_CHOICE', '¿Cuál palabra completa la frase? "La ___ alumbra por las noches".', ['Luna', 'Sol', 'Tierra', 'Nube'], 'Luna'],
        ['MULTIPLE_CHOICE', 'Todos los perros ladran. "Rex" es un perro. Por lo tanto:', ['Rex corre', 'Rex ladra', 'Rex duerme', 'Rex come'], 'Rex ladra'],
        ['MULTIPLE_CHOICE', 'Si hoy es martes, ¿qué día fue ayer?', ['Lunes', 'Miércoles', 'Domingo', 'Jueves'], 'Lunes'],
        ['MULTIPLE_CHOICE', '¿Cuál palabra NO pertenece al grupo?', ['Manzana', 'Pera', 'Plátano', 'Silla'], 'Silla'],
        ['MULTIPLE_CHOICE', '¿Qué sigue en este patrón? O, X, O, X, ...', ['O', 'X', 'A', 'B'], 'O'],

        // ==========================================
        // DIFICULTAD: FÁCILES (11-20)
        // ==========================================
        ['MULTIPLE_CHOICE', '¿Cuánto es 45 + 15 - 10?', ['40', '50', '60', '70'], '50'],
        ['MULTIPLE_CHOICE', 'Si tienes 24 lápices y los repartes en 4 cajas iguales, ¿cuántos hay por caja?', ['5', '6', '7', '8'], '6'],
        ['MULTIPLE_CHOICE', 'Completa la serie: 5, 10, 20, 40, ...', ['50', '60', '70', '80'], '80'],
        ['MULTIPLE_CHOICE', 'PERRO es a JAURÍA como PEZ es a:', ['Agua', 'Cardumen', 'Red', 'Escama'], 'Cardumen'],
        ['MULTIPLE_CHOICE', '¿Cuál de estas palabras significa casi lo mismo que "Rápido"?', ['Lento', 'Veloz', 'Pausado', 'Quietud'], 'Veloz'],
        ['MULTIPLE_CHOICE', '¿Cuál es la palabra que falta? "El ___ corre por los rieles".', ['Coche', 'Barco', 'Tren', 'Avión'], 'Tren'],
        ['MULTIPLE_CHOICE', 'Si A es mayor que B, y B es mayor que C, entonces:', ['A es mayor que C', 'C es mayor que A', 'A es igual a C', 'No se sabe'], 'A es mayor que C'],
        ['MULTIPLE_CHOICE', 'Si no es de día y no está lloviendo, ¿cómo está el cielo?', ['Soleado', 'Despejado de noche', 'Nublado', 'Con arcoíris'], 'Despejado de noche'],
        ['MULTIPLE_CHOICE', '¿Cuál de estas palabras NO pertenece al grupo?', ['Verde', 'Azul', 'Rojo', 'Dulce'], 'Dulce'],
        ['MULTIPLE_CHOICE', '¿Qué número sigue el patrón? 1, 3, 5, 7, ...', ['8', '9', '10', '11'], '9'],

        // ==========================================
        // DIFICULTAD: MEDIA (21-35)
        // ==========================================
        ['MULTIPLE_CHOICE', 'Un tren recorre 120 km en 2 horas. ¿A qué velocidad promedio va?', ['40 km/h', '50 km/h', '60 km/h', '80 km/h'], '60 km/h'],
        ['MULTIPLE_CHOICE', '¿Cuál es el 25% de 200?', ['25', '40', '50', '75'], '50'],
        ['MULTIPLE_CHOICE', 'Si 3 cuadernos cuestan $12, ¿cuánto cuestan 7 cuadernos?', ['$24', '$28', '$30', '$32'], '$28'],
        ['MULTIPLE_CHOICE', 'Completa la serie: 1, 4, 9, 16, ...', ['20', '24', '25', '30'], '25'],
        ['MULTIPLE_CHOICE', '¿Cuánto es (8 x 4) / 2?', ['8', '12', '16', '32'], '16'],
        ['MULTIPLE_CHOICE', 'PÁGINA es a LIBRO como TECLA es a:', ['Pantalla', 'Ratón', 'Teclado', 'Escritorio'], 'Teclado'],
        ['MULTIPLE_CHOICE', '¿Cuál palabra es sinónimo de "Abundante"?', ['Escaso', 'Mucho', 'Pequeño', 'Breve'], 'Mucho'],
        ['MULTIPLE_CHOICE', 'Seleccione la analogía correcta: MARTILLO : CLAVO ::', ['Destornillador : Madera', 'Serrucho : Cortar', 'Pincel : Pintura', 'Llave : Tuerca'], 'Llave : Tuerca'],
        ['MULTIPLE_CHOICE', '¿Cuál es el antónimo de "Efímero"?', ['Corto', 'Pasajero', 'Duradero', 'Ligero'], 'Duradero'],
        ['MULTIPLE_CHOICE', 'Si algunos médicos son deportistas y todos los deportistas son sanos:', ['Algunos médicos son sanos', 'Todos los médicos son sanos', 'Ningún médico es sano', 'Los médicos no hacen deporte'], 'Algunos médicos son sanos'],
        ['MULTIPLE_CHOICE', 'Si el reloj marca las 3:15 y lo giras 90 grados a la derecha, ¿hacia dónde apunta el minutero?', ['A las 6', 'A las 9', 'A las 12', 'A las 3'], 'A las 6'],
        ['MULTIPLE_CHOICE', 'Si todos los lunes trabajo y hoy no estoy trabajando, se deduce que:', ['Es martes', 'Hoy no es lunes', 'Mañana es lunes', 'Ayer fue domingo'], 'Hoy no es lunes'],
        ['MULTIPLE_CHOICE', '¿Cuál palabra NO pertenece al grupo?', ['Caminar', 'Correr', 'Saltar', 'Dormir'], 'Dormir'],
        ['MULTIPLE_CHOICE', '¿Qué palabra NO pertenece al grupo?', ['Arquitecto', 'Ingeniero', 'Abogado', 'Edificio'], 'Edificio'],
        ['MULTIPLE_CHOICE', 'Completa la secuencia: A1, B2, C3, ...', ['D4', 'E5', 'C4', 'B3'], 'D4'],

        // ==========================================
        // DIFICULTAD: DIFÍCILES (36-45)
        // ==========================================
        ['MULTIPLE_CHOICE', 'Un artículo tiene un 20% de descuento y cuesta $80. ¿Cuál era su precio original?', ['$90', '$100', '$110', '$120'], '$100'],
        ['MULTIPLE_CHOICE', 'Si 5 máquinas hacen 5 artículos en 5 minutos, ¿cuánto tardan 100 máquinas en hacer 100 artículos?', ['1 min', '5 min', '20 min', '100 min'], '5 min'],
        ['MULTIPLE_CHOICE', '¿Qué número es la mitad de la cuarta parte de 80?', ['5', '10', '20', '40'], '10'],
        ['MULTIPLE_CHOICE', 'Completa la serie: 2, 6, 12, 20, 30, ...', ['36', '40', '42', '50'], '42'],
        ['MULTIPLE_CHOICE', '¿Cuál palabra es el antónimo de "Altruista"?', ['Generoso', 'Egoísta', 'Bondadoso', 'Humilde'], 'Egoísta'],
        ['MULTIPLE_CHOICE', 'Seleccione la opción correcta: CÉLULA es a TEJIDO como:', ['Tejido es a órgano', 'Átomo es a electrón', 'Rueda es a coche', 'Libro es a estante'], 'Tejido es a órgano'],
        ['MULTIPLE_CHOICE', '¿Cuál de estas frases es gramaticalmente correcta?', ['Ellos caminaron ayer', 'Ellos caminarán ayer', 'Ellos camina ayer', 'Ellos caminando ayer'], 'Ellos caminaron ayer'],
        ['MULTIPLE_CHOICE', '¿Cuál de estas palabras es la más alejada del significado de "Diligente"?', ['Vago', 'Activo', 'Rápido', 'Cuidadoso'], 'Vago'],
        ['MULTIPLE_CHOICE', 'Seis amigos se sientan en círculo. Si Juan no está al lado de Pedro ni de Luis, y Luis está al lado de Ana...', ['Ana está al lado de Juan', 'Pedro está al lado de Luis', 'Ana está frente a Juan', 'No se puede determinar'], 'No se puede determinar'],
        ['MULTIPLE_CHOICE', '¿Qué palabra NO pertenece al grupo?', ['Oído', 'Vista', 'Tacto', 'Corazón'], 'Corazón'],

        // ==========================================
        // DIFICULTAD: MUY DIFÍCILES (46-50)
        // ==========================================
        ['MULTIPLE_CHOICE', 'Si el ayer del pasado mañana es jueves, ¿qué día será el mañana del ayer de pasado mañana?', ['Sábado', 'Viernes', 'Domingo', 'Jueves'], 'Sábado'],
        ['MULTIPLE_CHOICE', 'En un grupo de 30 personas, 15 hablan inglés, 12 hablan francés y 5 hablan ambos. ¿Cuántas no hablan ninguno?', ['3', '8', '10', '13'], '8'],
        ['MULTIPLE_CHOICE', '¿Cuál es el resultado de (2^3 + √16) x 2?', ['16', '18', '20', '24'], '24'],
        ['MULTIPLE_CHOICE', 'Si "BOCA" se codifica como "CPDB", ¿cómo se codifica "CASA"?', ['DBTB', 'DBST', 'EBTC', 'DBTA'], 'DBTB'],
        ['MULTIPLE_CHOICE', '¿Qué palabra NO pertenece al grupo?', ['Platón', 'Aristóteles', 'Sócrates', 'Newton'], 'Newton']
    ];

    // 3. Insertar preguntas
    $insertedCount = 0;
    $stmtInsert = $pdo->prepare("INSERT INTO catalog_questions (id, testId, type, questionText, options, correctAnswer, points, isActive) VALUES (?, ?, ?, ?, ?, ?, 1, 1)");

    foreach ($questions as $q) {
        $id = uniqid('CQ-WOND-');
        $type = $q[0];
        $text = $q[1];
        $options = json_encode($q[2]);
        $answer = $q[3];
        
        $stmtInsert->execute([$id, $testId, $type, $text, $options, $answer]);
        $insertedCount++;
    }

    echo "¡Proceso finalizado con éxito!\n";
    echo "Insertadas $insertedCount preguntas en la prueba ID: $testId.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
