<script setup lang="ts">
import type { TemplateMetadata } from '~/types/resume'

defineProps<{
    thumbnail: TemplateMetadata['thumbnail']
}>()
</script>

<template>
    <div :class="['aspect-[3/4] rounded-xl overflow-hidden', thumbnail.bg]">
        <div :class="['w-full h-full flex', thumbnail.mainClass]">
            <template v-for="(element, idx) in thumbnail.elements" :key="idx">
                <div :class="element.class">
                    <template v-if="element.children">
                        <template v-for="(child, cidx) in element.children" :key="cidx">
                            <div :class="child.class">
                                <template v-if="child.children">
                                    <div v-for="(grandchild, gidx) in child.children" :key="gidx"
                                        :class="grandchild.class"></div>
                                </template>
                            </div>
                        </template>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>
