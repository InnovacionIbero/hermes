<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Corporación Universitaria Iberoamericana 2023</span>
        </div>
    </div>
</footer>

<!--===============================================================================================-->
<!-- Bootstrap core JavaScript-->


<script src="{{asset('general/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{asset('general/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('general/js/sb-admin-2.min.js')}}"></script>

<!-- Font awesome for Icons-->
<script src="https://kit.fontawesome.com/def3229fdd.js" crossorigin="anonymous"></script>

<!--Tooltip-->
<script>
    var j = jQuery.noConflict();
    j(document).ready(function() {
        j('[data-toggle="tooltip"]').tooltip();

        var urlActual = window.location.href;
        const botonSolicitud = document.getElementById('botonSolicitud');
        const solicitudContainer = document.getElementById('solicitudContainer');

        botonSolicitud.addEventListener('click', () => {
            if (solicitudContainer.style.display === 'none' || solicitudContainer.style.display === '') {
                solicitudContainer.style.display = 'block';
            } else {
                solicitudContainer.style.display = 'none';
            }
        });

        const botonCodigos = document.getElementById('botonCodProgramas');
        const divBuscarCodigos = document.getElementById('buscarCodigoPrograma');

        botonCodigos.addEventListener('click', () => {
            if (divBuscarCodigos.style.display === 'none' || divBuscarCodigos.style.display === '') {
                divBuscarCodigos.style.display = 'block';
            } else {
                divBuscarCodigos.style.display = 'none';
            }
        });


        $('#formSolicitud').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("email.solicitud") }}',
                method: 'POST',
                data: {
                    formData,
                    url: urlActual
                },
                success: function(response) {
        
                    if (response == 'enviado') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Solicitud enviada',
                            text: 'Tu solicitud ha sido enviada, el equipo la resolverá lo más pronto posible.',
                            confirmButtonText: 'Gracias!'
                        })
                        $('#solicitud').val('');
                        $('#solicitudContainer').css('display', 'none');
                    }
                },

            });

        });

        codigosProgramas()

        function codigosProgramas() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('programas.buscador') }}",
                cache: false,
                contentType: false,
                processData: false,
                success: function(datos) {
                    if (datos != null) {
                        try {
                            datos = jQuery.parseJSON(datos);
                        } catch {
                            datos = datos;
                        }
                        $.each(datos, function(key, value) {
                            $('#codigosProgramas').append(`<li id="Checkbox${value.codprograma}" class="nombresProgramas d-none" data-codigo="${value.codprograma}"> ${value.programa} - ${value.Facultad}</li>`);
                        });
                    }
                },
                error: function() {
                    $('#programas').append('<h5>No hay programas</h5>')
                }
            })
        }


        $("#buscadorCodigosProgramas").on("input", function() {
            var textoBuscado = $(this).val().toLowerCase(); 
            if(textoBuscado){
                $(".nombresProgramas").each(function() {
                        var codigoElemento = $(this).data("codigo").toLowerCase();
                        if (codigoElemento.includes(textoBuscado)) {
                        $(this).removeClass('d-none'); 
                        } else {
                        $(this).addClass('d-none'); 
                        }
                
                });
            }else{
                $(document).find('#codigosProgramas li').addClass('d-none');
            }
        });
    });
</script>

</body>

</html>