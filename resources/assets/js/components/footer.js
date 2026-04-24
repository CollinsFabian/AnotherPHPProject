import { loadTemplate } from "../template.js";

const footerTemplatePath = '/assets/templates/components/footer.html';

export async function renderFooter() {
    return loadTemplate(footerTemplatePath);
}
