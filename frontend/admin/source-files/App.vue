<template>
  <div></div>
  <Teleport v-if="firstNoticeTarget && message" :to="firstNoticeTarget">
    <div v-html="message"></div>
  </Teleport>
  <Teleport v-if="processingLoadingTarget" :to="processingLoadingTarget">
    <div style="display: flex; align-items: center; gap: 6px; font-style: italic; margin-top: 10px;">
      <UiSpinner v-if="processingLoading" /> Products processing.. <span v-if="processingPage">Next
        page to process is {{ processingPage }}</span>
    </div>
  </Teleport>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue';
import { checkSubscribe, getMessage, getMetaKeysProgress, postSubscribe } from './assets/js/api';
import UiSpinner from './components/UiSpinner.vue';

console.log('vue3 with vite loaded!!')

const message = ref<string>('');
const firstNoticeTarget = ref<string>('');
const processingLoadingTarget = ref<string>('');
const processingLoading = ref<boolean>(false);
const processingPage = ref<number>(0);

const init = async () => {
  const firstNotice = document.querySelector('#ms-generic-message-container') as HTMLElement
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const url = (window as any)?.ms_data?.externalUrlMessage
  const data = await getMessage({ url });
  // console.log('data:', data)
  if (data?.message) {
    message.value = data.message

    if (firstNotice) {
      firstNotice.style.display = 'block'
      firstNoticeTarget.value = '#ms-generic-message-container #ms-generic-message'
    }
  }

  const siteUrl = (document.querySelector('input#msSiteUrl[type="hidden"]') as HTMLInputElement)?.value;
  if (siteUrl) {
    const subscribeData = await checkSubscribe({ host: siteUrl });
    console.log('subscribe data: ', subscribeData)

    if (subscribeData.subscribed !== undefined) {
      if (!subscribeData.subscribed) {
        // if not subscribed show form in notice
        const notice = document.querySelector('#ms-subscribe-notice-container') as HTMLElement
        if (notice) {
          notice.style.display = 'block';

          const form = notice.querySelector('#ms-subscribe-form');
          const input = notice.querySelector('input#msAdminEmail') as HTMLInputElement
          if (form && input) {
            form.addEventListener('submit', async (eve: Event) => {
              eve.preventDefault();
              console.log(eve)
              if (!siteUrl || !input?.value) return

              const response = await postSubscribe({
                host: siteUrl,
                email: input?.value,
              })

              if (response?.subscribed) {
                notice.style.display = 'none';
              }
            })
          }
        }
      }
      // else if subscribed do nothing
    }
  }
}

const setupFreemiumWarning = () => {
  const checkbox = document.querySelector('#ms-settings-field-freemium-activated') as HTMLInputElement;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const defaults = (window as any).ms_data?.settings?.defaults;


  if (checkbox && defaults && Object.keys(defaults).length > 0) {
    checkbox.addEventListener('click', (eve: Event) => {
      console.log('click')
      let changedValues = false;

      for (const def of Object.keys(defaults)) {
        const val = defaults[def];
        const input = document.querySelector(`#${def}`) as HTMLInputElement;
        // console.log('input:', input, val, input?.value)
        if (input?.value !== val) {
          changedValues = true;
        }
      }

      if (changedValues) {
        console.log('changed values');

        if (!confirm('Are you sure you want to deactivate freemium features? Some freemium fields have been changed, and will be reverted back to the defaults.')) {
          eve.stopPropagation();
          eve?.preventDefault()
        }
      }
    })
  }
}

const wait = (ms: number) => {
  return new Promise(resolve => setTimeout(resolve, ms));
}

const metaKeysClickListener = async (eve: Event) => {
  console.log('processing click', eve)
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const metaKeysEndpoint = (window as any)?.ms_data?.externalUrlMetaKeysCreation;

  eve.target?.removeEventListener('click', metaKeysClickListener);
  do {
    processingLoading.value = true;
    if (document.querySelector('#ms-meta-keys-progress')) {
      processingLoadingTarget.value = '#ms-meta-keys-progress';
    }
    const response = await getMetaKeysProgress({ url: metaKeysEndpoint });
    if (response?.nextPageToProcess && response?.nextPageToProcess !== 0) {
      processingPage.value = response.nextPageToProcess;
    }
    else if (response?.nextPageToProcess === 0 || !!response?.nextPageToProcess) {
      window.location.reload();
      processingLoading.value = false;
      processingLoadingTarget.value = '';
      processingPage.value = 0;
    }

    await wait(1000);
  } while (processingLoading.value)
}

const setupProcessing = () => {
  const processing = document.querySelector('#ms-start-products-creation-ajax') as HTMLAnchorElement;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const metaKeysEndpoint = (window as any)?.ms_data?.externalUrlMetaKeysCreation;

  if (processing && metaKeysEndpoint) {
    processing.addEventListener('click', metaKeysClickListener)
  }
}

onMounted(async () => {
  await init();
  setupFreemiumWarning();
  setupProcessing();
})
</script>

<style scoped></style>
