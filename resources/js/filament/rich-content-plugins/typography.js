// Extension TipTap de l'éditeur de l'administration : police et taille choisies dans une liste
// fermée (voir app/Filament/Support/RichText/FontMark.php et SizeMark.php, à garder synchronisés).
// Utilise le TipTap déjà chargé par Filament : aucune compilation nécessaire.
export default function () {
    const { Extension, Mark } = window.FilamentRichEditor.tiptap.core

    const choiceMark = (name, attribute, values) =>
        Mark.create({
            name,
            addAttributes() {
                return {
                    value: {
                        default: null,
                        parseHTML: (element) => element.getAttribute(attribute),
                        renderHTML: (attributes) => (attributes.value ? { [attribute]: attributes.value } : {}),
                    },
                }
            },
            parseHTML() {
                return [{ tag: `span[${attribute}]`, getAttrs: (element) => (values.includes(element.getAttribute(attribute)) ? null : false) }]
            },
            renderHTML({ HTMLAttributes }) {
                return ['span', HTMLAttributes, 0]
            },
        })

    return Extension.create({
        name: 'emsiTypography',
        addExtensions() {
            return [
                choiceMark('emsiFont', 'data-font', ['display', 'serif', 'mono']),
                choiceMark('emsiSize', 'data-size', ['sm', 'lg', 'xl', '2xl']),
            ]
        },
    })
}
