<script setup>
import { ref, computed } from "vue";
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "text",
    },
    modelValue: {
        type: [String, Number],
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const showPassword = ref(false);
const isPassword = computed(() => props.type === "password");
const inputType = computed(() =>
    isPassword.value ? (showPassword.value ? "text" : "password") : props.type
);
</script>

<template>
    <div class="mb-3">
        <label :for="props.id" class="form-label fw-semibold text-success">{{
            props.label
        }}</label>
        <div class="input-icon-group">
            <slot name="icon"></slot>
            <input
                :id="props.id"
                :type="inputType"
                :value="props.modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                class="form-control input-green"
                :class="{ 'is-invalid': props.error }"
                :required="props.required"
                autocomplete="off"
            />
            <button
                v-if="isPassword"
                type="button"
                class="toggle-password-btn"
                tabindex="-1"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
                <i
                    :class="
                        showPassword
                            ? 'fa-solid fa-eye-slash'
                            : 'fa-solid fa-eye'
                    "
                ></i>
            </button>
        </div>
        <div
            v-if="props.error"
            class="invalid-feedback text-danger fw-semibold error-animate error-below-input"
            style="display: block"
        >
            {{ props.error }}
        </div>
    </div>
</template>

<style scoped>
.input-icon-group {
    position: relative;
    display: flex;
    align-items: center;
}
.input-icon-group > ::v-slotted(*) {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    color: #198754cc;
    font-size: 1.1rem;
    pointer-events: none;
}
.input-green {
    border-radius: 0.7rem;
    border: 1.5px solid #e0e0e0;
    box-shadow: 0 1px 6px rgba(25, 135, 84, 0.04);
    transition: border-color 0.22s, box-shadow 0.22s, background 0.22s;
    font-size: 1.08rem;
    padding: 0.7rem 1rem 0.7rem 2.5rem;
    background: #f8fafc;
}
.input-green:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.18rem rgba(25, 135, 84, 0.18);
    background: #e9f7ef;
}
.is-invalid.input-green {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.13);
}
.toggle-password-btn {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #198754cc;
    font-size: 1.15rem;
    cursor: pointer;
    z-index: 3;
    padding: 0 0.2rem;
    transition: color 0.18s;
}
.toggle-password-btn:focus {
    outline: none;
    color: #198754;
}
.toggle-password-btn:hover {
    color: #157347;
}
.error-animate {
    animation: shake 0.3s;
}
.error-below-input {
    margin-top: 0.25rem;
    text-align: left;
    width: 100%;
    position: static;
    display: block;
}
@keyframes shake {
    10%,
    90% {
        transform: translateX(-2px);
    }
    20%,
    80% {
        transform: translateX(4px);
    }
    30%,
    50%,
    70% {
        transform: translateX(-8px);
    }
    40%,
    60% {
        transform: translateX(8px);
    }
}
</style>
