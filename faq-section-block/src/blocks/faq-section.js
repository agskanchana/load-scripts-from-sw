import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    InnerBlocks,
    useBlockProps,
} from '@wordpress/block-editor';

/**
 * Register FAQ Section block
 */
export default function registerFaqSectionBlock() {
    registerBlockType('ekwa/faq-section', {
        title: __('FAQ Section'),
        icon: 'format-chat',
        category: 'design',
        description: __('A block for organizing FAQ content with schema markup.'),

        attributes: {
            blockId: {
                type: 'string',
                default: ''
            }
        },

        edit: ({ attributes, setAttributes }) => {
            const { blockId } = attributes;
            const blockProps = useBlockProps({
                className: 'ekwa-faq-section'
            });

            // Generate a unique ID for the block if none exists
            if (!blockId) {
                setAttributes({ blockId: 'faq-' + Date.now().toString(36) });
            }

            return (
                <div {...blockProps}>
                    <div className="ekwa-faq-content">
                        <InnerBlocks />
                    </div>
                </div>
            );
        },

        save: ({ attributes }) => {
            const { blockId } = attributes;
            const blockProps = useBlockProps.save({
                className: 'ekwa-faq-section',
                id: blockId,
                'data-schema-added': 'true'
            });

            return (
                <div {...blockProps}>
                    <script dangerouslySetInnerHTML={{
                        __html: `
                            (function() {
                                var htmlAttribute = document.querySelector('html');
                                htmlAttribute.setAttribute("itemscope", " ");
                                htmlAttribute.setAttribute("itemtype", "https://schema.org/FAQPage");
                            })();
                        `
                    }}></script>
                    <div className="ekwa-faq-content" itemScope itemprop="mainEntity" itemType="https://schema.org/Question">
                        <InnerBlocks.Content />
                    </div>
                </div>
            );
        },

        deprecated: [
            {
                // Version with itemScope on content but without script
                attributes: {
                    blockId: {
                        type: 'string',
                        default: ''
                    }
                },

                save: ({ attributes }) => {
                    const { blockId } = attributes;
                    const blockProps = useBlockProps.save({
                        className: 'ekwa-faq-section',
                        id: blockId
                    });

                    return (
                        <div {...blockProps}>
                            <div className="ekwa-faq-content" itemScope itemprop="mainEntity" itemType="https://schema.org/Question">
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            },
            {
                // Even older version without schema
                attributes: {
                    blockId: {
                        type: 'string',
                        default: ''
                    }
                },

                save: ({ attributes }) => {
                    const { blockId } = attributes;

                    return (
                        <div className="ekwa-faq-section" id={blockId}>
                            <div className="ekwa-faq-content">
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            },
        ],
    });
}