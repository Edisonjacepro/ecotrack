import { startStimulusApp } from '@symfony/stimulus-bundle';
import CarbonFormController from './controllers/carbon_form_controller.js';

const app = startStimulusApp();
app.register('carbon-form', CarbonFormController);