@extends('layouts.admin')
@section('title', 'Configurar Módulos — ' . $event->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.show', $event) }}" class="text-gray-500 hover:text-gray-800 text-sm">&larr; Volver</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Configurar Módulos — {{ $event->name }}</h1>
    <p class="text-gray-500 text-sm mt-1">Personaliza el contenido de cada sección de la landing page</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Modules list --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow p-5 sticky top-4">
            <h3 class="font-semibold text-gray-700 mb-4">Módulos disponibles</h3>
            <ul class="space-y-2 text-sm">
    @php
                $moduleLabels = ['hero' => 'Hero / Inicio', 'info' => 'Información', 'contact' => 'Contactos', 'pdf' => 'Documento PDF', 'map' => 'Mapa Turístico', 'emergency' => 'Números de Emergencia', 'video_intro' => 'Video Anuncio'];
                @endphp
            @foreach($event->modules as $loop_module)
                    <li class="flex items-center gap-1">
                        {{-- Botones ↑↓ --}}
                        <div class="flex flex-col gap-0.5 shrink-0">
                            <form method="POST" action="{{ route('admin.events.modules.reorder', [$event, $loop_module]) }}">
                                @csrf
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" title="Subir"
                                        class="p-1 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition {{ $loop->first ? 'invisible' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.events.modules.reorder', [$event, $loop_module]) }}">
                                @csrf
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" title="Bajar"
                                        class="p-1 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition {{ $loop->last ? 'invisible' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        {{-- Enlace al módulo --}}
                        <a href="#module-{{ $loop_module->id }}" 
                           class="flex items-center gap-2 flex-1 p-2 rounded-lg hover:bg-gray-50 transition {{ $loop_module->is_active ? 'text-gray-800' : 'text-gray-400' }}">
                            <span class="w-2 h-2 rounded-full shrink-0 {{ $loop_module->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            <span class="font-medium text-xs">{{ $moduleLabels[$loop_module->type] ?? ucfirst($loop_module->type) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    
    {{-- Module forms --}}
    <div class="lg:col-span-2 space-y-6">
        
        @foreach($event->modules as $module)
            <div id="module-{{ $module->id }}" class="bg-white rounded-xl shadow p-6">
                
                <form method="POST" action="{{ route('admin.events.modules.update', [$event, $module]) }}"
                      {!! in_array($module->type, ['emergency', 'video_intro']) ? 'enctype="multipart/form-data"' : '' !!}>
                    @csrf
                    @method('PATCH')
                    
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">
                                @php $labels = ['hero'=>'Hero / Inicio','info'=>'Información','contact'=>'Contactos','pdf'=>'Documento PDF','map'=>'Mapa Turístico','emergency'=>'Números de Emergencia','video_intro'=>'Video Anuncio']; @endphp
                                {{ $labels[$module->type] ?? ucfirst($module->type) }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                @if($module->type === 'hero') Hero con carrusel y logo
                                @elseif($module->type === 'info') Información general del evento
                                @elseif($module->type === 'contact') Contactos importantes
                                @elseif($module->type === 'pdf') Documento PDF embebido
                                @elseif($module->type === 'map') Mapa turístico interactivo
                                @elseif($module->type === 'emergency') Números de emergencia con logo y teléfono
                                @elseif($module->type === 'video_intro') Video anuncio overlay al abrir la página
                                @else Módulo personalizado
                                @endif
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $module->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                    
                    @if($module->type === 'info')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                <input type="text" name="settings[title]" value="{{ $module->settings['title'] ?? 'Información General' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                <textarea name="settings[content]" rows="6"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ $module->settings['content'] ?? '' }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Markdown y HTML básico soportados</p>
                            </div>

                            {{-- Lugar del Evento --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Lugar del Evento</p>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del lugar</label>
                                        <input type="text" name="settings[location]" value="{{ $module->settings['location'] ?? '' }}"
                                            placeholder="Ej: Estadio Olímpico Atahualpa, Quito"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <p class="text-xs text-gray-400 mt-1">Texto que se mostrará al visitante</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Búsqueda en Google Maps</label>
                                        <input type="text" name="settings[location_query]" value="{{ $module->settings['location_query'] ?? '' }}"
                                            placeholder="Ej: Estadio Atahualpa Quito Ecuador"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <p class="text-xs text-gray-400 mt-1">Dirección o lugar exacto para el botón de navegación</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Fechas del Evento --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Fecha del Evento</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio</label>
                                        <input type="date" name="settings[date_start]" value="{{ $module->settings['date_start'] ?? '' }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de fin <span class="text-gray-400 font-normal">(opcional)</span></label>
                                        <input type="date" name="settings[date_end]" value="{{ $module->settings['date_end'] ?? '' }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <p class="text-xs text-gray-400 mt-1">Dejar vacío si es un solo día</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Transmisión en Vivo --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Transmisión en Vivo</p>
                                <label class="flex items-start gap-3 cursor-pointer mb-3">
                                    <input type="hidden" name="settings[live_stream_button]" value="0">
                                    <input type="checkbox" name="settings[live_stream_button]" value="1"
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 mt-0.5"
                                           {{ filter_var($module->settings['live_stream_button'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Mostrar botón "Ver transmisión en vivo"</span>
                                        <p class="text-xs text-gray-400">Abre el Video Anuncio al hacer click. Requiere que el módulo Video Anuncio esté activo y con video configurado.</p>
                                    </div>
                                </label>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtexto de la tarjeta</label>
                                    <input type="text" name="settings[live_stream_label]"
                                           value="{{ $module->settings['live_stream_label'] ?? 'Video oficial del evento' }}"
                                           placeholder="Ej: Video oficial del evento"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Texto descriptivo que aparece bajo el título de la tarjeta</p>
                                </div>
                            </div>

                            {{-- Resultados del Evento --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Resultados del Evento</p>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Enlace de resultados <span class="text-gray-400 font-normal">(opcional)</span></label>
                                <input type="url" name="settings[results_url]"
                                       value="{{ $module->settings['results_url'] ?? '' }}"
                                       placeholder="https://..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                                <p class="text-xs text-gray-400 mt-1">Si se configura, muestra el botón "Ver Resultados" que abre esta página en una nueva pestaña</p>
                            </div>
                        </div>
                    @elseif($module->type === 'contact')
                        <div class="space-y-5"
                             x-data="{
                                contacts: {{ json_encode(array_values($module->settings['contacts'] ?? [['name'=>'','role'=>'','email'=>'','phone'=>'']])) }},
                                addContact() {
                                    this.contacts.push({ name: '', role: '', email: '', phone: '' });
                                },
                                removeContact(i) {
                                    if (this.contacts.length > 1) this.contacts.splice(i, 1);
                                }
                             }">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título de la sección</label>
                                <input type="text" name="settings[title]"
                                    value="{{ $module->settings['title'] ?? 'Contactos Importantes' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>

                            {{-- Contact rows --}}
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-3">Contactos</p>
                                <div class="space-y-3">
                                    <template x-for="(contact, i) in contacts" :key="i">
                                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 relative group">

                                            {{-- Remove button --}}
                                            <button type="button" @click="removeContact(i)"
                                                    :disabled="contacts.length === 1"
                                                    class="absolute top-3 right-3 text-gray-300 hover:text-red-500 disabled:opacity-20 disabled:cursor-not-allowed transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>

                                            {{-- Badge --}}
                                            <span class="text-[10px] font-bold uppercase tracking-wide text-gray-400 mb-3 block"
                                                  x-text="'Contacto ' + (i + 1)"></span>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-6">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Nombre / Grado</label>
                                                    <input type="text" x-model="contact.name"
                                                           placeholder="Crnl. Juan Pérez"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Cargo / Rol</label>
                                                    <input type="text" x-model="contact.role"
                                                           placeholder="Director del Evento"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                                                    <input type="email" x-model="contact.email"
                                                           placeholder="correo@fedeme.app"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Teléfono</label>
                                                    <input type="tel" x-model="contact.phone"
                                                           placeholder="+593 99 123 4567"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Add contact button --}}
                            <button type="button" @click="addContact()"
                                    class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar contacto
                            </button>

                            {{-- Hidden input: serialized JSON for the controller --}}
                            <input type="hidden" name="settings[contacts]"
                                   x-effect="$el.value = JSON.stringify(contacts)">

                        </div>
                    @elseif($module->type === 'pdf')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título del documento</label>
                                <input type="text" name="settings[title]"
                                    value="{{ $module->settings['title'] ?? 'Documentos' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Ej: Reglamento del Evento">
                                <p class="text-xs text-gray-400 mt-1">Nombre que verán los visitantes en la sección y en el botón.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL pública del PDF</label>
                                <input type="url" name="settings[pdf_url]"
                                    value="{{ $module->settings['pdf_url'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono"
                                    placeholder="https://drive.google.com/file/d/.../view">
                                <p class="text-xs text-gray-400 mt-1">El PDF debe ser accesible públicamente. Soporta Google Drive, Dropbox, OneDrive o cualquier URL directa.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción opcional</label>
                                <textarea name="settings[description]" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Breve descripción del contenido del documento...">{{ $module->settings['description'] ?? '' }}</textarea>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                                <p class="font-semibold mb-1">💡 Cómo obtener el enlace público</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li><strong>Google Drive:</strong> Archivo → Compartir → cualquiera con el enlace → copiar enlace</li>
                                    <li><strong>Dropbox:</strong> Compartir → crear enlace → cambiar <code>dl=0</code> por <code>raw=1</code></li>
                                    <li><strong>OneDrive:</strong> Compartir → cualquiera con el enlace → copiar</li>
                                    <li>También puede ser un PDF alojado directamente en tu servidor</li>
                                </ul>
                            </div>
                        </div>
                    @elseif($module->type === 'map')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título de la sección</label>
                                <input type="text" name="settings[title]"
                                    value="{{ $module->settings['title'] ?? 'Mapa Turístico' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Ej: Conoce Quito">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Búsqueda en Google Maps</label>
                                <input type="text" name="settings[map_query]"
                                    value="{{ $module->settings['map_query'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Ej: Quito, lugares turisticos">
                                <p class="text-xs text-gray-400 mt-1">Escribe exactamente lo que buscarías en Google Maps. El mapa mostrará esos resultados.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción opcional</label>
                                <textarea name="settings[description]" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Ej: Explora los principales atractivos turísticos de la ciudad sede del evento.">{{ $module->settings['description'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Altura del mapa</label>
                                <select name="settings[map_height]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    @foreach(['400' => 'Compacto (400px)', '550' => 'Normal (550px)', '700' => 'Grande (700px)'] as $val => $label)
                                        <option value="{{ $val }}" {{ ($module->settings['map_height'] ?? '550') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Preview --}}
                            @if(!empty($module->settings['map_query']))
                            <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden">
                                <p class="text-xs text-gray-500 px-4 pt-3 pb-1 font-medium">Vista previa</p>
                                <iframe
                                    src="https://maps.google.com/maps?q={{ urlencode($module->settings['map_query']) }}&output=embed&hl=es"
                                    class="w-full" style="height:300px" frameborder="0" loading="lazy"
                                    allowfullscreen></iframe>
                            </div>
                            @endif
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                <p class="font-semibold mb-1">💡 Ejemplos de búsqueda</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li><code>Quito, lugares turisticos</code> — Lugares turísticos de Quito</li>
                                    <li><code>Guayaquil, restaurantes</code> — Restaurantes en Guayaquil</li>
                                    <li><code>Centro Histórico Quito</code> — Zoom al centro histórico</li>
                                    <li><code>Hotel Dann Carlton Quito</code> — Ubicación exacta de un lugar</li>
                                </ul>
                            </div>
                        </div>
                    @elseif($module->type === 'emergency')
                        <div class="space-y-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título de la sección</label>
                                <input type="text" name="settings[title]"
                                    value="{{ $module->settings['title'] ?? 'Número de Emergencias' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logo o imagen</label>
                                @if(!empty($module->settings['logo_path']))
                                    <div class="mb-3 flex items-center gap-4">
                                        <img src="{{ asset('storage/' . $module->settings['logo_path']) }}"
                                             alt="Logo actual"
                                             class="w-20 h-20 object-contain rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
                                        <div>
                                            <p class="text-xs text-gray-500 font-mono">{{ basename($module->settings['logo_path']) }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Sube una nueva imagen para reemplazarla</p>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="logo" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG o SVG — máx. 2 MB</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de teléfono</label>
                                <input type="tel" name="settings[phone]"
                                       value="{{ $module->settings['phone'] ?? '' }}"
                                       placeholder="Ej: 911  o  +593 98 765 4321"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono font-bold tracking-wide">
                                <p class="text-xs text-gray-400 mt-1">Se mostrará como enlace de marcación directa en móviles</p>
                            </div>

                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">
                                <p class="font-semibold mb-1">💡 Consejo</p>
                                <p class="text-xs">El botón de teléfono genera un enlace <code>tel:</code> para marcar directamente desde el móvil del visitante.</p>
                            </div>

                        </div>
                    @elseif($module->type === 'video_intro')
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título del anuncio <span class="text-gray-400 font-normal">(opcional)</span></label>
                                <input type="text" name="settings[title]"
                                       value="{{ $module->settings['title'] ?? '' }}"
                                       placeholder="Ej: Mensaje oficial del presidente"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>

                            {{-- URL externa --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Opción 1 — URL de video externo</p>
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL del video</label>
                                <input type="url" name="settings[video_url]"
                                       value="{{ $module->settings['video_url'] ?? '' }}"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                                <p class="text-xs text-gray-400 mt-1">Acepta YouTube, Vimeo o URL directa de .mp4. Si se configura, tiene prioridad sobre el archivo subido.</p>
                            </div>

                            {{-- Upload --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Opción 2 — Subir archivo de video</p>
                                @if(!empty($module->settings['video_path']))
                                    <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <svg class="w-8 h-8 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.867v6.266a1 1 0 01-1.447.902L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                        </svg>
                                        <div>
                                            <p class="text-xs text-gray-500 font-mono">{{ basename($module->settings['video_path']) }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Sube un nuevo archivo para reemplazarlo</p>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="video" accept="video/mp4,video/webm"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-400 mt-1">MP4 o WebM — máx. 100 MB</p>
                            </div>

                            {{-- show_once --}}
                            <div class="border-t border-gray-100 pt-4">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="hidden" name="settings[show_once]" value="0">
                                    <input type="checkbox" name="settings[show_once]" value="1"
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600"
                                           {{ filter_var($module->settings['show_once'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Mostrar solo una vez por visita</span>
                                        <p class="text-xs text-gray-400">El visitante no verá el anuncio de nuevo mientras no cierre su navegador</p>
                                    </div>
                                </label>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                <p class="font-semibold mb-1">💡 Funcionamiento</p>
                                <p class="text-xs">El video aparece como overlay al abrir la página. El visitante puede pausarlo o cerrarlo con Esc o el botón X. Los videos subidos directamente se reproducen sin sonido por defecto (requisito del navegador para autoplay).</p>
                            </div>
                        </div>
                    @elseif($module->type === 'hero')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título personalizado</label>
                                <input type="text" name="settings[title]" value="{{ $module->settings['title'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Usar nombre del evento por defecto">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                                <textarea name="settings[subtitle]" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Descripción breve">{{ $module->settings['subtitle'] ?? '' }}</textarea>
                            </div>

                            {{-- Toggle carrusel --}}
                            <div class="border-t border-gray-100 pt-4">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="hidden" name="settings[show_carousel]" value="0">
                                    <input type="checkbox" name="settings[show_carousel]" value="1"
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 mt-0.5"
                                           {{ filter_var($module->settings['show_carousel'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Mostrar sección de carrusel</span>
                                        <p class="text-xs text-gray-400">Si está desactivado, la sección de imágenes no aparece en la página aunque haya imágenes subidas</p>
                                    </div>
                                </label>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                <p class="font-semibold mb-1">💡 Carrusel e Imágenes</p>
                                <p>El logo y las imágenes del carrusel se configuran desde la sección <strong>Editar Evento</strong>.</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-6 py-2 rounded-lg transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
                
            </div>
        @endforeach
        
    </div>
    
</div>
@endsection
