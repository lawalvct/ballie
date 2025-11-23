@extends('layouts.tenant')

@section('title', 'Help & Documentation')

@push('styles')
<style>[v-cloak] { display: none; }</style>
@endpush

@section('content')
<div id="helpApp" v-cloak>
    <quick-links></quick-links>
    <help-content></help-content>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
<script>
const { createApp } = Vue;

createApp({
    components: {
        @include('tenant.help.components.quick-links')
        'help-content': {
            template: `
                <div class="container mx-auto px-4 pb-8">
                    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-8">
                        <about-section></about-section>
                        <getting-started></getting-started>
                        <modules-overview></modules-overview>
                        <faq-section></faq-section>
                        <support-section></support-section>
                    </div>
                </div>
            `,
            components: {
                @include('tenant.help.components.about-section')
                @include('tenant.help.components.getting-started')
                @include('tenant.help.components.modules-overview')
                @include('tenant.help.components.faq-section')
                @include('tenant.help.components.support-section')
            }
        }
    }
}).mount('#helpApp');
</script>
@endpush
