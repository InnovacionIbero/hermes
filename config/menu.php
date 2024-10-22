<?php

  // Estructuras de menú para diferentes roles
  return [

    /** menu de admin  */
    'admin' => 
    [
        [
          'id'    =>'Admisiones',
          'icon'  =>'fa-solid fa-sack-dollar',
          'title' =>'Admisiones',          
          'route' => "home.mafi"
        ],
        [
          'id'    =>'Moodle',
          'icon'  =>'fa-solid fa-graduation-cap',
          'title' => 'Campus (Abiertos)',              
          'route' => "home.moodle"
        ],
        [
          'id'    =>'MoodleCerrado',
          'icon'  =>'fa-solid fa-graduation-cap',
          'title' => 'Campus (Cerrados)',              
          'route' => "home.moodleCerrado"
        ],
        [
          'id'=>'Planeación',
          'icon'=>'fa-solid fa-lightbulb',
          'title' => 'Planeación',          
          'route' => "home.planeacion"
        ],
        [
          'id'=>'AlertasTempranas',
          'icon'=>'fa-solid fa-bell',
          'title' => 'Alertas Tempranas
                      (Programación-Planeación)',
          'route' => "alertas.inicio"
        ],
        [
          'id'=>'Duplicados',
          'icon'=>'fa-solid fa-triangle-exclamation',
          'title' => 'Duplicados Moodle', 
          'route' => "duplicados.sistema"
        ],
    
        [
          'id'=>'Solicitudes',
          'icon'=>'fa-solid fa-triangle-exclamation',
          'title' => 'Solicitudes Sistema', 
          'route' => "solicitudes.sistema"
        ],
        [
          'id'=>'GestiónUsuarios',
          'icon'=>'fas fa-address-book',
          'title' =>'Gestión de usuarios', 
          'submenu' => 
            [
              [
                'title' => 'Usuarios',
                'route' => "admin.users",
                'id' => 'menuUsuarios'
              ],
              [
                'title' => 'Roles',               
                'route' => "admin.roles",
                'id' => 'menuRoles'
              ],
            ]
        ],
      
        [
          'id'=>'Config',
          'icon'=>'fa-solid fa-gears',
          'title' => 'Configuración',
          'submenu' => 
            [
              [
                'title' => 'Facultades',          
                'route' => "admin.facultades",
                'id' => "menuFacultades"
              ],
              [
                'title' => 'Pregrado',            
                'route' => "facultad.programas",
                'id' => "menuProgramas"
              ],
              [
                'title' => 'Planeación',          
                'route' => "planeacion.view",
                'id' => "menuTablaPlaneacion"
              ],
              [
                'title' => 'Especialización',     
                'route' => "facultad.especializacion",
                'id' => "menuEspecializacion"
              ],
              [
                'title' => 'Maestría',            
                'route' => "facultad.maestria",
                'id' => "menuMaestria"
              ],
              [
                'title' => 'Educación continua',  
                'route' => "facultad.continua",
                'id' => "menuEducacion"
              ],
              [
                'title' => 'Periodos',            
                'route' => "facultad.periodos",
                'id' => "menuPeriodos"
              ],
              [
                'title' => 'Reglas de negocio',   
                'route' => "facultad.reglas",
                'id' => "menuReglas"
              ],
              [
                'title' => 'Periodos - Programas',
                'route' => "programasPeriodos.view",
                'id' => "menuPeridosProgramas"
              ],
              [
                'title' => 'Metas',
                'route' => "metas.view",
                'id'=>'metasMenu'
              ],
            ]
        ],
        
        [
          'id'=>'Perfil',
          'icon'=>'fa-solid fa-user-gear',
          'title' => 'Perfil',
          'submenu' => 
            [
              [
                'title' => 'Ver perfil',
                'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
                'id'=>'menuVerPerfil'
              ],
              [
                'title' => 'Cambiar contraseña',
                'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
                'id'=>'menuCambiar'
              ],
            ]
        ],
    
        // [
        //   'id'=>'Pausa',
        //   'icon'=>'fa-solid fa-user-gear',
        //   'title' => 'Pausa activa',
        //   'submenu' => 
        //     [
        //       [
        //         'title' => 'Asteroides',
        //         'route' => "route('pausa.asteroides',['id'=>encrypt(auth()->user()->id)])"
        //       ],
        //     ]
        // ],
      
        [ 
          'id'=>'Salir',
          'icon'=>'fas fa-sign-out-alt',
          'title' => 'Salir',
          'route' => "logout"
        ],

    ],

    //** menu por defecto para todos los roles */
    'usuario_normal' => 
    [
      [
        'id'=>'Admisiones',
   
        'icon'  =>'fa-solid fa-sack-dollar',
        'title' =>'Admisiones',          
        'route' => "home.mafi"
      ],

      [
        'id'=>'Moodle',
   
        'icon'  =>'fa-solid fa-graduation-cap',
         'title' => 'Campus (Abiertos)',              
        'route' => "home.moodle"
      ],
      [
        'id'    =>'MoodleCerrado',
        'icon'  =>'fa-solid fa-graduation-cap',
        'title' => 'Campus (Cerrados)',              
        'route' => "home.moodleCerrado"
      ],

      [
        'id'=>'Planeación',
   
        'icon'=>'fa-solid fa-lightbulb',
        'title' => 'Planeación',          
        'route' => "home.planeacion"
      ],

      [
        'id'=>'Alertas',
        'icon'=>'fa-solid fa-bell',
        'title' => 'Alertas Tempranas
                    (Programación-Planeación)',
        'route' => "alertas.inicio"
      ],
     
      [ 
        
        'id'=>'Perfil',
   
        'icon'=>'fa-solid fa-user-gear',
        'title' => 'Perfil',
        'submenu' => 
          [
            [
              'title' => 'Ver perfil',
              'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
              'id'=>'menuVerPerfil'
            ],
            [
              'title' => 'Cambiar contraseña',
              'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
              'id'=>'menuCambiar'
            ],
          ]
      ],
  
      [ 
        'id'=>'Salir',
        'icon'=>'fas fa-sign-out-alt',
        'title' => 'Salir',
        'route' => "logout"
      ],

    ],

    /** menu de usuarios  que solo tengan acceso a moodle */
    'usuario_moodle' => 
    [
    
      [
        'id'=>'Moodle',
   
        'icon'  =>'fa-solid fa-graduation-cap',
         'title' => 'Campus (Abiertos)',              
        'route' => "home.moodle"
      ],
      // [
      //   'id'    =>'MoodleCerrado',
      //   'icon'  =>'fa-solid fa-graduation-cap',
      //   'title' => 'Campus (Cerrados)',              
      //   'route' => "home.moodleCerrado"
      // ],
     
      [ 
        
        'id'=>'Perfil',
   
        'icon'=>'fa-solid fa-user-gear',
        'title' => 'Perfil',
        'submenu' => 
          [
            [
              'title' => 'Ver perfil',
              'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
              'id'=>'menuVerPerfil'
            ],
            [
              'title' => 'Cambiar contraseña',
              'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
              'id'=>'menuCambiar'
            ],
          ]
      ],
    
      [ 
        'id'=>'Planeación',
   
        'icon'=>'fas fa-sign-out-alt',
        'title' => 'Salir',
        'route' => "logout"
      ],
      
    ],

    /** menu usuario con solo acceso a la planeacion academica */
    'usuario_planeacion' => 
    [
      [
        'id'=>'Planeación',
   
        'icon'=>'fa-solid fa-lightbulb',
        'title' => 'Planeación',          
        'route' => "home.planeacion"
      ],
    
      [
        'id'=>'Perfil',
   
        'icon'=>'fa-solid fa-user-gear',
        'title' => 'Perfil',
        'submenu' => 
          [
            [
              'title' => 'Ver perfil',
              'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
              'id'=>'menuVerPerfil'
            ],
            [
              'title' => 'Cambiar contraseña',
              'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
              'id'=>'menuCambiar'
            ],
          ]
      ],
          
      [ 
        'id'=>'Salir',
   
        'icon'=>'fas fa-sign-out-alt',
        'title' => 'Salir',
        'route' => "logout"
      ],
    ],

    /** menu de usuario solo acceso a admisiones*/
    'usuario_admisiones' => 
    [
      [
        'id'=>'Admisiones',
   
        'icon'  =>'fa-solid fa-sack-dollar',
        'title' =>'Admisiones',          
        'route' => "home.mafi"
      ],
  
      [
        'id'=>'Perfil',
   
        'icon'=>'fa-solid fa-user-gear',
        'title' => 'Perfil',
        'submenu' => 
          [
            [
              'title' => 'Ver perfil',
              'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
              'id'=>'menuVerPerfil'
            ],
            [
              'title' => 'Cambiar contraseña',
              'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
              'id'=>'menuCambiar'
            ],
          ]
      ],
      
      [ 
        'id'=>'Salir',
   
        'icon'=>'fas fa-sign-out-alt',
        'title' => 'Salir',
        'route' => "logout"
      ],
    ],

     /** menu de admin  */
    'comercial' => 
    [
      [
        'id'=>'Admisiones',
   
        'icon'  =>'fa-solid fa-sack-dollar',
        'title' =>'Admisiones',          
        'route' => "home.mafi"
      ],
      [
        'id'=>'Moodle',
   
        'icon'  =>'fa-solid fa-graduation-cap',
         'title' => 'Campus (Abiertos)',              
        'route' => "home.moodle"
      ], 
      [
        'id'    =>'MoodleCerrado',
        'icon'  =>'fa-solid fa-graduation-cap',
        'title' => 'Campus (Cerrados)',              
        'route' => "home.moodleCerrado"
      ],
      [
        'id'=>'Perfil',
        'icon'=>'fa-solid fa-user-gear',
        'title' => 'Perfil',
        'submenu' => 
          [
            [
              'title' => 'Ver perfil',
              'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
              'id'=>'menuVerPerfil'
            ],
            [
              'title' => 'Cambiar contraseña',
              'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
              'id'=>'menuCambiar'
            ],
          ]
      ],
      [ 
        'id'=>'Salir',
        'icon'=>'fas fa-sign-out-alt',
        'title' => 'Salir',
        'route' => "logout"
      ],

    ],


     /** menu de usuarios  que solo tengan acceso a moodle */
     'usuario_transversal' => 
     [
    
      [
        'id'=>'Moodle',
   
        'icon'  =>'fa-solid fa-graduation-cap',
         'title' => 'Campus (Abiertos)',              
        'route' => "home.moodle"
      ],
      
      [
        'id'=>'Planeación',
   
        'icon'=>'fa-solid fa-lightbulb',
        'title' => 'Planeación',          
        'route' => "home.planeacion"
      ],
     
      [
        'id'=>'Perfil',
        'icon'=>'fa-solid fa-user-gear',
        'title' => 'Perfil',
        'submenu' => 
          [
            [
              'title' => 'Ver perfil',
              'route' => "user.perfil, ['id'=>encrypt(auth()->user()->id)]",
              'id'=>'menuVerPerfil'
            ],
            [
              'title' => 'Cambiar contraseña',
              'route' => "cambio.cambio,['idbanner'=>encrypt(auth()->user()->id_banner)]",
              'id'=>'menuCambiar'
            ],
          ]
      ],
    
      [ 
        'id'=>'Salir',
   
        'icon'=>'fas fa-sign-out-alt',
        'title' => 'Salir',
        'route' => "logout"
      ],
      
    ],
    


  ];

?>