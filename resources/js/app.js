import '../css/app.css';

import { createApp } from 'vue';

// Create Vue app for Onboarding
const onboardingElement = document.getElementById('onboarding-tutorial');
if (onboardingElement) {
    // Import the OnboardingTutorial component only when needed
    import('./onboarding/OnboardingTutorial.vue').then(({ default: OnboardingTutorial }) => {
        const onboardingApp = createApp(OnboardingTutorial);

        onboardingApp.mount('#onboarding-tutorial');
    });
}
