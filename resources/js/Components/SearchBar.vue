<template>
    <div :class="['searchbar-modern', customClass]">
        <span class="searchbar-icon-left"><i class="fa fa-search"></i></span>
        <input
            type="text"
            class="form-control searchbar-input"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :aria-label="placeholder"
            @keyup.enter="$emit('search', modelValue)"
        />
        <button
            v-if="modelValue"
            class="searchbar-clear-btn"
            type="button"
            @click="$emit('update:modelValue', '')"
            title="Clear"
            aria-label="Clear search"
        >
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>

<script setup>
defineProps({
    modelValue: String,
    placeholder: {
        type: String,
        default: "Search...",
    },
    customClass: {
        type: String,
        default: '',
    },
});
</script>

<style scoped>
.searchbar-modern {
    position: relative;
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 999px;
    box-shadow: 0 2px 8px rgba(30,41,59,0.08);
    max-width: 350px;
    width: 100%;
    padding: 0 12px;
    height: 40px;
}
.searchbar-input {
    border: none;
    outline: none;
    background: transparent;
    flex: 1;
    padding-left: 32px;
    padding-right: 32px;
    font-size: 1rem;
    height: 38px;
    border-radius: 999px;
    box-shadow: none;
    color: #222;
}
.searchbar-input:focus {
    background: #f8fafc;
}
.searchbar-icon-left {
    position: absolute;
    left: 16px;
    color: #198754;
    font-size: 1.1rem;
    pointer-events: none;
}
.searchbar-clear-btn {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #888;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0;
    transition: color 0.2s;
    z-index: 2;
}
.searchbar-clear-btn:hover {
    color: #dc3545;
}
</style>
