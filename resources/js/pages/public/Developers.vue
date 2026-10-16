<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Terminal } from '@lucide/vue';

const props = defineProps<{ baseUrl: string }>();

const endpoints = [
    { method: 'GET', path: '/api/startups', desc: 'List verified startups. Params: verified, category, per_page (≤100).' },
    { method: 'GET', path: '/api/startups/{slug}', desc: 'Full profile of one startup, including MRR history.' },
    { method: 'GET', path: '/api/leaderboard', desc: 'Top startups by verified MRR. Param: limit (≤100).' },
];

const apiCurl = `curl ${props.baseUrl}/api/leaderboard?limit=10`;
const mcpCurl = `curl -X POST ${props.baseUrl}/mcp -H "Content-Type: application/json" -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'`;
const mcpConfig = `{
  "mcpServers": {
    "afrimrr": { "url": "${props.baseUrl}/mcp" }
  }
}`;

function copy(text: string) {
    navigator.clipboard.writeText(text).then(() => toast.success('Copied'));
}
</script>

<template>
    <Head title="Developers — API & MCP" />

    <div class="mx-auto max-w-4xl px-4 py-14">
        <h1 class="text-3xl font-extrabold tracking-tight">Developers</h1>
        <p class="mt-2 text-muted-foreground">
            A public, no-auth JSON API and an MCP server over AfriMRR's verified-revenue data.
        </p>

        <!-- REST API -->
        <section class="mt-10">
            <h2 class="text-xl font-bold">REST API</h2>
            <div class="mt-4 space-y-3">
                <div v-for="e in endpoints" :key="e.path" class="rounded-xl border border-border bg-card p-4">
                    <div class="flex items-center gap-3">
                        <span class="rounded-md bg-emerald-600/10 px-2 py-0.5 text-xs font-bold text-emerald-600">{{ e.method }}</span>
                        <code class="text-sm font-semibold">{{ e.path }}</code>
                    </div>
                    <p class="mt-2 text-sm text-muted-foreground">{{ e.desc }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <code class="flex-1 overflow-x-auto rounded-md bg-muted px-3 py-2 text-xs">{{ apiCurl }}</code>
                <button class="shrink-0 rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500" @click="copy(apiCurl)">Copy</button>
            </div>
        </section>

        <!-- MCP -->
        <section class="mt-12">
            <h2 class="flex items-center gap-2 text-xl font-bold"><Terminal class="size-5 text-emerald-600" /> MCP server</h2>
            <p class="mt-2 text-sm text-muted-foreground">
                Streamable-HTTP <a href="https://modelcontextprotocol.io" target="_blank" rel="noopener" class="text-emerald-600 hover:underline">Model Context Protocol</a>
                endpoint. Point any MCP client at it to let an AI agent query verified startup data.
            </p>
            <div class="mt-4 rounded-xl border border-border bg-card p-4">
                <p class="text-sm"><span class="font-semibold">Endpoint:</span> <code>{{ props.baseUrl }}/mcp</code></p>
                <p class="mt-2 text-sm"><span class="font-semibold">Tools:</span> <code>search_startups</code>, <code>get_startup</code>, <code>leaderboard</code></p>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <code class="flex-1 overflow-x-auto rounded-md bg-muted px-3 py-2 text-xs">{{ mcpCurl }}</code>
                <button class="shrink-0 rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500" @click="copy(mcpCurl)">Copy</button>
            </div>
            <pre class="mt-3 overflow-x-auto rounded-xl border border-border bg-card p-4 text-xs"><code>{{ mcpConfig }}</code></pre>
        </section>

        <!-- llms.txt -->
        <section class="mt-12">
            <h2 class="text-xl font-bold">For LLMs</h2>
            <p class="mt-2 text-sm text-muted-foreground">
                Machine-readable summary at
                <a :href="`${props.baseUrl}/llms.txt`" class="text-emerald-600 hover:underline">/llms.txt</a>,
                sitemap at <a :href="`${props.baseUrl}/sitemap.xml`" class="text-emerald-600 hover:underline">/sitemap.xml</a>.
            </p>
        </section>
    </div>
</template>
