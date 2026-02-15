<script setup lang="ts">
import { onMounted, ref } from 'vue'

const apiStatus = ref<string>('Checking...')

onMounted(async () => {
  try {
    const response = await fetch('https://localhost:8443/api/health')
    const data = await response.json()
    apiStatus.value = `✅ API OK: ${data.service}`
  } catch (error) {
    apiStatus.value = `❌ API Error: ${error}`
  }
})
</script>

<template>
  <div>
    <h1>ChartPlaylister - Dev</h1>
    <p>API Status: {{ apiStatus }}</p>
  </div>
</template>