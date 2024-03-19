<template>

    <div class="d-flex justify-content-between flex-wrap">
        <p ref="cmd" v-on:focus="$event.target.select()" readonly>
            <code>{{ command }}</code>
        </p>
        <button type="button"
                class="btn btn-sm d-inline-flex width-100 height-30 align-items-center justify-content-center"
                :class="isCopied ? 'btn-success' : 'btn-warning'"
                @mouseleave="afterCopied()"
                @click="copy()"
        >
                        <span v-if="isCopied" :key="'check1'">
                            <app-icon name="check" class="size-18 mr-2"/> {{ $t('copied') }}
                        </span>
                        <span v-else :key="'copy1'">
                            <app-icon name="copy" class="size-18 mr-2"/> {{ $t('copy') }}
                        </span>
        </button>
    </div>
</template>

<script>

export default {
    name: "CommandViewWithCopy",
    props: ['command'],
    data() {
        return {
            isCopied: false,
        }
    },
    methods: {
        copy() {
            let input = document.createElement("textarea");
            input.value = this.command;
            document.body.appendChild(input);
            input.select();
            document.execCommand("Copy");
            input.remove();
            this.isCopied = true;
        },
        afterCopied() {
            setTimeout(() => {
                this.isCopied = false;
            }, 1000)
        }
    },

}
</script>

