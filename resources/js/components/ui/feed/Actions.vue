<template>
<!--<div :class="`absolute bottom-[-30px] ${$props.message.internal ? 'left-0' : 'right-0'}`">-->
<div class="mt-.5">
  <div class="relative inline-block text-left">
    <div>
      <a href="javascript:;" @click.prevent="toggle()" class="inline-flex w-full justify-center text-xs !text-gray-400 no-underline pl-0 font-mono">
        {{ translate('Aktionen') }}
        <svg class="h-5 w-5 mt-[-2px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
      </a>
    </div>
    <div :class="[isOpen ? 'block' : 'hidden', `absolute left-0 z-10 mt-2 w-48 origin-top-right rounded-sm bg-white shadow-sm ring-2 ring-dark ring-opacity-5 focus:outline-none menu-inner-${$props.message.uuid}`]" 
      role="menu" 
      aria-orientation="vertical" 
      aria-labelledby="menu-button" 
      tabindex="-1">
      <div role="none">

        <div>
          <a href="javascript:;" 
            @click.prevent="$emit('reply', $props.message.uuid)" 
            class="text-xs !text-gray-400 no-underline font-mono flex items-center leading-none hover:bg-light p-2 min-h-[40px]">
            <reply-icon class="w-4 h-4 mr-3" />
            {{ translate('Antworten') }}
          </a>
        </div>
        <div>
          <a href="javascript:;" 
            @click.prevent="$emit('delete', $props.message.uuid)" 
            class="text-xs !text-gray-400 no-underline font-mono flex items-center leading-none hover:bg-light p-2 min-h-[40px]" 
            v-if="$props.canDelete">
            <trash-icon class="w-4 h-4 mr-3" />
            {{ translate('Löschen') }}
          </a>
        </div>
        <div>
          <a href="javascript:;" 
            class="text-xs !text-gray-400 no-underline font-mono flex items-center leading-none hover:bg-light p-2 min-h-[40px]"
            @click="$emit('translate', 'en-GB')">
            <flag-en-icon class="mr-3" />
            {{ translate('Translate') }}
          </a>
        </div>
        <div>
          <a href="javascript:;" 
            class="text-xs !text-gray-400 no-underline font-mono flex items-center leading-none hover:bg-light p-2 min-h-[40px]"
            @click="$emit('translate', 'de')">
            <flag-de-icon class="mr-3" />
            {{ translate('Übersetzen') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</template>
<script>
import { TrashIcon, ReplyIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";
import FlagDeIcon from '@/components/ui/icons/flag-de';
import FlagEnIcon from '@/components/ui/icons/flag-en';

export default {

  components: {
    TrashIcon,
    ReplyIcon,
    FlagDeIcon,
    FlagEnIcon,
  },

  mixins: [i18n],

  data() {
    return {
      isOpen: false,
    };
  },

  props: {

    message: {
      type: Object,
    },

    canDelete: {
      type: Boolean,
      default: false,
    }
  },

  mounted() {
    this.addListeners();
  },

  methods: {
    toggle() {
      this.isOpen = this.isOpen ? false : true;
    },

    hide() {
      this.isOpen = false;
    },

    addListeners() {
      const menuInner = document.querySelector(`.menu-inner-${this.$props.message.uuid}`);
      menuInner.addEventListener('mouseleave', ($event) => {
        this.hide();
      }, false);
      window.addEventListener('click', ($event) => { 
        const inner = document.querySelector('.menu-inner');
        if ($event.target.contains(inner) && event.target !== inner) {
          this.hide();
        }
      }, false);
    }
  },

}
</script>
