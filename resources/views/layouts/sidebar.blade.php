<div id="kt_app_sidebar"
     class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="{{ route('default') }}">
            <img alt="Logo" src="{{ asset('storage/'.$app_info->app_logo ?? '') }}" class="h-60px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('storage/'.$app_info->app_logo_small ?? '') }}" class="h-30px app-sidebar-logo-minimize" />
        </a>
        <div
            id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <span class="svg-icon svg-icon-2 rotate-180">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor" />
                    <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor" />
                </svg>
            </span>
        </div>
    </div>
{{--    @if(!Str::contains(url()->current(), 'dashboard'))--}}
        <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
            <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y mt-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
                <div class="menu menu-column menu-rounded menu-sub-indention px-3 pb-20" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                    @php
                        $arrSubModul = [];
                        $arrListMenu = [];
                        $arrListChildMenu = [];
                    @endphp
                    @foreach($app_sub_moduls as $subModul)
                        @if($subModul->menus->isNotEmpty())
                            @foreach($subModul->menus as $menu)
                                @can('view '.$selected_modul->target."/".$menu->target)
                                    @php
                                        if($menu->parent_id == null){
                                            $arrSubModul[$subModul->id] = $subModul;
                                            $arrListMenu[$subModul->id][$menu->order] = $menu;
                                        }else{
                                            $arrListChildMenu[$menu->parent_id][$menu->id] = $menu;
                                        }
                                    @endphp
                                @endcan
                            @endforeach
                        @endif
                    @endforeach

                    @foreach($arrSubModul as $subModul)
                        <div class="menu-item pt-0">
                            <div class="menu-content">
                                <span class="menu-heading fw-bold text-uppercase fs-7">{{ $subModul->name }}</span>
                            </div>
                        </div>
                        @php
                            ksort($arrListMenu[$subModul->id]);
                        @endphp
                        @foreach($arrListMenu[$subModul->id] as $menu)
                            @if(!isset($arrListChildMenu[$menu->id]))
                                @php
                                    $routeMenu = implode(".", array($selected_modul->target, $menu->target, "index"));
                                @endphp
                                <div class="menu-item">
                                    <a class="menu-link app-menu {{ $menu->target == $selected_menu->target ? "active" : "" }}"
                                       @if($menu->target == $selected_menu->target)
                                           style="background-color: {{ $app_info->primary_color }} !important;"
                                       @endif

                                           @if(Route::has($routeMenu))
                                           href="{{ route($routeMenu) }}"
                                        @endif
                                    >
                                        <span class="menu-icon"
                                        >
                                            <i class="{{ $menu->icon ?: 'fa-solid fa-user-group' }} fs-5"
                                               @if($menu->target == $selected_menu->target)
                                                   style="color: #ffffff !important;"
                                                @else
                                                    style="color: #000000 !important;"
                                                @endif
                                            ></i>
                                        </span>
                                        <span class="menu-title app-menu-title sidebar"
                                              @if($menu->target == $selected_menu->target)
                                                  style="color: #ffffff !important;"
                                              @else
                                                    style="color: #000000 !important;"
                                              @endif
                                        >{{ $menu->name }}</span>
                                    </a>
                                </div>
                            @else
                                @php
                                    $isActiveParent = false;
                                    foreach($arrListChildMenu[$menu->id] as $child) {
                                        if ($child->target == $selected_menu->target) {
                                            $isActiveParent = true;
                                            break;
                                        }
                                    }
                                    $routeMenu = implode(".", array($selected_modul->target, $menu->target, "index"));
                                    $selectedChildClassParent = $isActiveParent ? "here show" : "";
                                @endphp
                                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $selectedChildClassParent }}">
                                    <a class="menu-link {{ $menu->target == $selected_menu->target ? "active" : "" }}"
                                       @if($menu->target == $selected_menu->target)
                                           style="background-color: {{ $app_info->primary_color }} !important;"
                                       @endif
                                       @if(Route::has($routeMenu))
                                           href="{{ route($routeMenu) }}"
                                        @endif
                                    >
                                        <span class="menu-icon">
                                            <i class="{{ $menu->icon ?: 'fa-solid fa-user-group' }} fs-5" style="color: #000000 !important;"></i>
                                        </span>
                                        <span class="menu-title app-menu-title sidebar" style="color: #000000 !important;">{{ $menu->name }}</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="menu-sub menu-sub-accordion">
                                    @foreach($arrListChildMenu[$menu->id] as $child)
                                        @php
                                            $routeChild = implode(".", array($selected_modul->target, $child->target, "index"));
                                        @endphp
                                        <div class="menu-item">
                                            <a class="menu-link {{ $child->target == $selected_menu->target ? "active" : "" }}"
                                               @if($child->target == $selected_menu->target)
                                                   style="background-color: {{ $app_info->primary_color }} !important;"
                                               @endif
                                               @if(Route::has($routeChild))
                                                   href="{{ route($routeChild) }}"
                                                @endif
                                            >
                                            <span class="menu-icon">
                                                <i class="{{ $child->icon ?: 'fa-solid fa-user-group' }} fs-5"
                                                   @if($child->target == $selected_menu->target)
                                                       style="color: #ffffff !important;"
                                                   @else
                                                       style="color: #000000 !important;"
                                                @endif
                                                ></i>
                                                </span>
                                                <span class="menu-title app-menu-title sidebar"
                                                  @if($child->target == $selected_menu->target)
                                                      style="color: #ffffff !important;"
                                                  @else
                                                      style="color: #000000 !important;"
                                                   @endif
                                                >{{ $child->name }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
{{--    @endif--}}
</div>
<style>
    .app-menu:hover {
        background-color: {{ $app_info->primary_color }} !important;
        color: #ffffff !important;
        opacity: 0.8;
    }

    .app-menu-title:hover {
        color: #ffffff !important;
    }
</style>
