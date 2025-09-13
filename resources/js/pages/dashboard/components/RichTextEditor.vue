<template>
  <div class="rich-text-editor">
    <div class="toolbar border-b border-gray-300 dark:border-gray-600 p-2 bg-gray-50 dark:bg-gray-700 rounded-t-md">
      <div class="flex flex-wrap gap-1">
        <!-- Bold -->
        <button
          type="button"
          @click="execCommand('bold')"
          :class="['toolbar-btn', { active: isActive('bold') }]"
          title="Bold"
        >
          <strong>B</strong>
        </button>
        
        <!-- Italic -->
        <button
          type="button"
          @click="execCommand('italic')"
          :class="['toolbar-btn', { active: isActive('italic') }]"
          title="Italic"
        >
          <em>I</em>
        </button>
        
        <!-- Underline -->
        <button
          type="button"
          @click="execCommand('underline')"
          :class="['toolbar-btn', { active: isActive('underline') }]"
          title="Underline"
        >
          <u>U</u>
        </button>
        
        <div class="border-l border-gray-300 dark:border-gray-600 mx-1"></div>
        
        <!-- Unordered List -->
        <button
          type="button"
          @click="execCommand('insertUnorderedList')"
          :class="['toolbar-btn', { active: isActive('insertUnorderedList') }]"
          title="Bullet List"
        >
          •
        </button>
        
        <!-- Ordered List -->
        <button
          type="button"
          @click="execCommand('insertOrderedList')"
          :class="['toolbar-btn', { active: isActive('insertOrderedList') }]"
          title="Numbered List"
        >
          1.
        </button>
        
        <div class="border-l border-gray-300 dark:border-gray-600 mx-1"></div>
        
        <!-- Link -->
        <button
          type="button"
          @click="insertLink"
          class="toolbar-btn"
          title="Insert Link"
        >
          🔗
        </button>
        
        <!-- Clear Formatting -->
        <button
          type="button"
          @click="execCommand('removeFormat')"
          class="toolbar-btn"
          title="Clear Formatting"
        >
          ✕
        </button>
      </div>
    </div>
    
    <div
      ref="editor"
      class="editor-content min-h-[120px] p-3 border border-t-0 border-gray-300 dark:border-gray-600 rounded-b-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      contenteditable="true"
      :placeholder="placeholder"
      @input="onInput"
      @focus="onFocus"
      @blur="onBlur"
      @paste="onPaste"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Enter text...'
  }
})

const emit = defineEmits(['update:modelValue'])

const editor = ref(null)
const isFocused = ref(false)

onMounted(() => {
  if (editor.value) {
    editor.value.innerHTML = props.modelValue || ''
    
    // Add placeholder functionality
    updatePlaceholder()
  }
})

watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.innerHTML !== newValue) {
    editor.value.innerHTML = newValue || ''
    updatePlaceholder()
  }
})

const onInput = () => {
  const content = editor.value.innerHTML
  emit('update:modelValue', content)
  updatePlaceholder()
}

const onFocus = () => {
  isFocused.value = true
  updatePlaceholder()
}

const onBlur = () => {
  isFocused.value = false
  updatePlaceholder()
}

const onPaste = (e) => {
  // Prevent pasting of rich content, only allow plain text
  e.preventDefault()
  const text = e.clipboardData.getData('text/plain')
  document.execCommand('insertText', false, text)
}

const updatePlaceholder = () => {
  if (!editor.value) return
  
  const isEmpty = !editor.value.textContent.trim()
  
  if (isEmpty && !isFocused.value) {
    editor.value.setAttribute('data-placeholder', props.placeholder)
  } else {
    editor.value.removeAttribute('data-placeholder')
  }
}

const execCommand = (command, value = null) => {
  document.execCommand(command, false, value)
  editor.value.focus()
  onInput()
}

const isActive = (command) => {
  return document.queryCommandState(command)
}

const insertLink = () => {
  const url = prompt('Enter URL:')
  if (url) {
    execCommand('createLink', url)
  }
}
</script>

<style scoped>
.toolbar-btn {
  @apply px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors;
}

.toolbar-btn.active {
  @apply bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-600;
}

.editor-content[data-placeholder]:empty::before {
  content: attr(data-placeholder);
  @apply text-gray-400 dark:text-gray-500 pointer-events-none;
}

.editor-content:focus[data-placeholder]:empty::before {
  content: '';
}

/* Rich text content styling */
.editor-content :deep(strong) {
  font-weight: bold;
}

.editor-content :deep(em) {
  font-style: italic;
}

.editor-content :deep(u) {
  text-decoration: underline;
}

.editor-content :deep(ul) {
  list-style-type: disc;
  margin-left: 1.5rem;
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.editor-content :deep(ol) {
  list-style-type: decimal;
  margin-left: 1.5rem;
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.editor-content :deep(li) {
  margin-bottom: 0.25rem;
}

.editor-content :deep(a) {
  @apply text-blue-600 dark:text-blue-400 underline;
}

.editor-content :deep(p) {
  margin-bottom: 0.5rem;
}

.editor-content :deep(p:last-child) {
  margin-bottom: 0;
}
</style>
