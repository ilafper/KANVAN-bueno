$(document).ready(function () {
    //efecto cartas y eso
    // Permitir que las tareas sean arrastrables
    $(".task").on("dragstart", function (e) {
        e.originalEvent.dataTransfer.setData("text/plain", e.target.textContent);
    });

    // Permitir que las columnas acepten tareas arrastradas
    $(".column").on("dragover", function (e) {
        e.preventDefault();
        $(this).addClass("over");
    });

    // Eliminar la clase "over" cuando se deja de arrastrar
    $(".column").on("dragleave", function () {
        $(this).removeClass("over");
    });

    // Manejar el evento de soltar la tarea en una columna
    $(".column").on("drop", function (e) {
        e.preventDefault();
        $(this).removeClass("over");

        // Obtener el contenido de la tarea arrastrada
        const taskContent = e.originalEvent.dataTransfer.getData("text/plain");

        // Crear una nueva tarea y agregarla a la columna
        const newTask = $("<div>").addClass("task card mb-2").attr("draggable", true).text(taskContent);
        $(this).append(newTask);

        // Eliminar la tarea original (opcional, si no quieres duplicar tareas)
        $(".task").filter(function () {
            return $(this).text() === taskContent;
        }).not(newTask).remove();
    });

    /*OBTENER LOS COLABOLADORES */
    $(".nueva-tarea").click(function () {
        // Obtener usuarios disponibles para colaborar, excluyendo al creador
        $.ajax({
            url: "../php/obtenerUsuarios.php", // URL del archivo PHP para obtener los usuarios
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let lista = $("#colaboradores");
                    lista.empty(); // Limpiar la lista de colaboradores

                    // Recorrer los usuarios y agregar solo los que no son el creador
                    response.usuarios.forEach(usuario => {
                        lista.append(
                            `<label>
                                <input type="checkbox" value="${usuario.id}" class="colaborador-nuevo">
                                ${usuario.nombre}
                            </label><br>`
                        );
                    });
                } else {
                    alert("Error al obtener los usuarios.");
                }
            },
            error: function () {
                alert("Error al obtener la lista de usuarios.");
            }
        });
        // Mostrar el modal para crear nueva tarea
        $("#nuevaTareaModal").modal("show");
    });


    // Crear una nueva tarea
    $('#guardarTarea').click(function () {
        let titulo = $('#titulo').val();
        let descripcion = $('#descripcion').val();
        let colaboradores = [];
        
        // Obtener los colaboradores seleccionados
        $('#colaboradores input:checked').each(function () {
            colaboradores.push($(this).val());
        });
        // Obtener el ID del creador (si está en el front-end)
        let creador = $('#usuario_id').val();  // O lo que sea necesario para obtener el ID del creador
        // Enviar los datos al servidor
        $.ajax({
            url: '../php/nuevaTarea.php',
            method: 'POST',
            data: {
                titulo: titulo,
                descripcion: descripcion,
                colaboradores: colaboradores,  // Enviar los colaboradores
                creador: creador  // Enviar el ID del creador
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    alert(res.message);
                    $('#tareaModal').modal('hide');  // Cerrar el modal
                    $('#formTarea')[0].reset();  // Limpiar el formulario
                } else {
                    alert("Error: " + res.error);
                }
            },
            error: function () {
                alert("Error al guardar la tarea.");
            }
        });
    });


    /*MOSTRAR LAS TAREAS*/
    $.ajax({
        url: "../php/obtenerTareas.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                const tareas = response.tareas;
                let listaTareas = $("#idea");

                // Vaciar el contenedor antes de agregar las nuevas tareas
                listaTareas.empty();

                // Recorrer las tareas y agregar al contenedor
                tareas.forEach(tarea => {
                    listaTareas.append(`
                        <div class="tarea">
                            <h3>${tarea.titulo}</h3>
                            <p>${tarea.descripcion}</p>
                            <p><strong>Creador:</strong> ${tarea.creador}</p>
                            <p><strong>Colaboradores:</strong> ${tarea.colaboradores.join(', ')}</p>
                        </div>
                    `);
                });
            } else {
                alert("No se pudieron obtener las tareas.");
            }
        },
        error: function () {
            alert("Error al obtener las tareas.");
        }
    });
});