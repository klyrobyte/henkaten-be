@extends('layouts.admin')
@section('title', 'Site Configuration')

@section('content')
    <div style="padding: 6px 0 110px;">
        
        <div style="margin-bottom:18px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="section-title" style="margin-bottom:4px;">🌐 Site Configuration</div>
                <div class="mm-page-sub">Manage global theme, branding colors, and visual effects.</div>
            </div>
        </div>

        @if(session('success'))
            <div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:10px; margin-bottom:18px; font-family:'Roboto Condensed',sans-serif; font-weight:700; font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="card" style="padding:32px; border-radius:24px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; background: #ffffff;">
            <form action="{{ route('admin.site-config.update') }}" method="POST">
                @csrf
                
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:32px 24px;">
                    
                    {{-- Navbar Color --}}
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:700; font-family:'Inter', 'Roboto', sans-serif; font-size:12px; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em;">Navbar Color</label>
                        <div style="display:flex; align-items:center; gap:12px; padding:8px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="position:relative; width:40px; height:40px; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:2px solid #fff;">
                                <input type="color" name="navbar_color" value="{{ $configs['navbar_color'] ?? '#2E7D32' }}" 
                                    id="picker_navbar" style="position:absolute; top:-10px; left:-10px; width:60px; height:60px; border:none; padding:0; cursor:pointer;"
                                    oninput="updatePreview('navbar', this.value)">
                            </div>
                            <div style="flex:1; display:flex; align-items:center;">
                                <span style="color:#9ca3af; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:14px; margin-right:4px;">#</span>
                                <input type="text" id="hex_navbar" value="{{ str_replace('#', '', $configs['navbar_color'] ?? '2E7D32') }}" 
                                    style="width:100%; border:none; background:transparent; outline:none; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:14px; font-weight:600; color:#374151;"
                                    oninput="updatePreview('navbar', '#' + this.value)"
                                    maxlength="6">
                            </div>
                        </div>
                        <small style="color:#6b7280; font-size:11px; margin-top:2px;">Background color for top and bottom navigation bars.</small>
                    </div>

                    {{-- Primary Branding Color --}}
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:700; font-family:'Inter', 'Roboto', sans-serif; font-size:12px; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em;">Primary Branding Color</label>
                        <div style="display:flex; align-items:center; gap:12px; padding:8px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="position:relative; width:40px; height:40px; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:2px solid #fff;">
                                <input type="color" name="primary_color" value="{{ $configs['primary_color'] ?? '#2E7D32' }}" 
                                    id="picker_primary" style="position:absolute; top:-10px; left:-10px; width:60px; height:60px; border:none; padding:0; cursor:pointer;"
                                    oninput="updatePreview('primary', this.value)">
                            </div>
                            <div style="flex:1; display:flex; align-items:center;">
                                <span style="color:#9ca3af; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:14px; margin-right:4px;">#</span>
                                <input type="text" id="hex_primary" value="{{ str_replace('#', '', $configs['primary_color'] ?? '2E7D32') }}" 
                                    style="width:100%; border:none; background:transparent; outline:none; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:14px; font-weight:600; color:#374151;"
                                    oninput="updatePreview('primary', '#' + this.value)"
                                    maxlength="6">
                            </div>
                        </div>
                        <small style="color:#6b7280; font-size:11px; margin-top:2px;">Replaces main green color (Buttons, Headers, Icons).</small>
                    </div>

                    {{-- Secondary Branding Color --}}
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:700; font-family:'Inter', 'Roboto', sans-serif; font-size:12px; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em;">Secondary Branding Color</label>
                        <div style="display:flex; align-items:center; gap:12px; padding:8px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="position:relative; width:40px; height:40px; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:2px solid #fff;">
                                <input type="color" name="secondary_color" value="{{ $configs['secondary_color'] ?? '#729E3F' }}" 
                                    id="picker_secondary" style="position:absolute; top:-10px; left:-10px; width:60px; height:60px; border:none; padding:0; cursor:pointer;"
                                    oninput="updatePreview('secondary', this.value)">
                            </div>
                            <div style="flex:1; display:flex; align-items:center;">
                                <span style="color:#9ca3af; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:14px; margin-right:4px;">#</span>
                                <input type="text" id="hex_secondary" value="{{ str_replace('#', '', $configs['secondary_color'] ?? '729E3F') }}" 
                                    style="width:100%; border:none; background:transparent; outline:none; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:14px; font-weight:600; color:#374151;"
                                    oninput="updatePreview('secondary', '#' + this.value)"
                                    maxlength="6">
                            </div>
                        </div>
                        <small style="color:#6b7280; font-size:11px; margin-top:2px;">Replaces lighter green accents.</small>
                    </div>

                    {{-- Theme Effect --}}
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:700; font-family:'Inter', 'Roboto', sans-serif; font-size:12px; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em;">Visual Effect</label>
                        <div style="padding:8px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02); height: 58px; display:flex; align-items:center;">
                            <select name="theme_effect" style="width:100%; border:none; background:transparent; outline:none; font-family:'Inter', 'Roboto', sans-serif; font-weight:600; color:#374151; font-size:14px; cursor:pointer;"
                                onchange="updateEffect(this.value)">
                                <option value="normal" {{ ($configs['theme_effect'] ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal (Flat)</option>
                                <option value="glossy" {{ ($configs['theme_effect'] ?? 'normal') === 'glossy' ? 'selected' : '' }}>Glossy (Subtle Gradients)</option>
                            </select>
                        </div>
                        <small style="color:#6b7280; font-size:11px; margin-top:2px;">Changes how components are rendered.</small>
                    </div>

                </div>

                <div style="margin-top:40px; padding-top:24px; border-top:1px solid #f3f4f6; display:flex; gap:16px;">
                    <button type="submit" style="width:100%; padding:14px 24px; background:var(--brand-primary); color:#ffffff; border:none; border-radius:12px; font-family:'Inter', 'Roboto', sans-serif; font-weight:600; font-size:15px; cursor:pointer; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Save Configuration
                    </button>
                </div>
            </form>

            <form action="{{ route('admin.site-config.reset') }}" method="POST" onsubmit="return confirm('Reset themes to default?')" style="text-align: center; margin-top: 16px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-family:'Inter', 'Roboto', sans-serif; font-weight:600; font-size:13px; transition:color 0.2s; display:inline-flex; align-items:center; gap:6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                    Reset to Factory Default
                </button>
            </form>
        </div>

        {{-- Preview Section --}}
        <div style="margin-top:30px;">
            <div class="section-title" style="margin-bottom:12px; font-size:14px;">Preview Components</div>
            <div style="display:flex; flex-wrap:wrap; gap:12px;">
                <button class="btn btn-primary" style="pointer-events:none;">Sample Primary Button</button>
                <div style="padding:10px 20px; background:var(--brand-secondary); color:#fff; border-radius:10px; font-family:'Roboto Condensed',sans-serif; font-weight:700; font-size:12px;">
                    Sample Secondary Accent
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function updatePreview(type, val) {
            if (!val.startsWith('#') || val.length !== 7) return;
            
            document.getElementById('hex_' + type).value = val.replace('#', '');
            document.getElementById('picker_' + type).value = val;

            if (type === 'navbar') {
                document.documentElement.style.setProperty('--navbar-bg', val);
            } else if (type === 'primary') {
                document.documentElement.style.setProperty('--brand-primary', val);
            } else if (type === 'secondary') {
                document.documentElement.style.setProperty('--brand-secondary', val);
            }
        }

        function updateEffect(val) {
            if (val === 'glossy') {
                document.body.classList.add('theme-glossy');
            } else {
                document.body.classList.remove('theme-glossy');
            }
        }

        // Initialize hex fields correctly
        document.addEventListener('DOMContentLoaded', () => {
            ['navbar', 'primary', 'secondary'].forEach(type => {
                const picker = document.getElementById('picker_' + type);
                const hex = document.getElementById('hex_' + type);
                if (picker && hex) {
                    hex.value = picker.value.replace('#', '');
                }
            });
        });
    </script>
@endpush
