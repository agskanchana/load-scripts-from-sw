import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    InnerBlocks,
    useBlockProps,
} from '@wordpress/block-editor';

/**
 * Register FAQ Answer block (companion to FAQ Question)
 */
export default function registerFaqAnswerBlock() {
    registerBlockType('ekwa/faq-answer', {
        title: __('FAQ Answer'),
        icon: 'editor-textcolor',
        category: 'design',
        description: __('An answer block for use after a FAQ question.'),
        parent: ['ekwa/faq-section', 'core/column', 'core/group', 'core/row'], // Allow in FAQ Section, columns, groups, and rows// Allow in both FAQ Section and columns

        edit: () => {
            const blockProps = useBlockProps({
                className: 'ekwa-faq-answer'
            });

            // List of allowed blocks to include in this answer
            const ALLOWED_BLOCKS = [
                'core/paragraph',
                'core/list',
                'core/heading',
                'core/image',
                'acf/div-block' // ACF block - make sure this name matches your registered ACF block
            ];

            return (
                <div {...blockProps}>
                    <InnerBlocks
                        allowedBlocks={ALLOWED_BLOCKS}
                        template={[
                            ['core/paragraph', { placeholder: 'Write your answer here...' }]
                        ]}
                        templateLock={false} // Allow users to add/remove/reorder blocks
                        renderAppender={() => <InnerBlocks.ButtonBlockAppender />} // Add a button to add more blocks
                    />
                </div>
            );
        },

        save: () => {
            const blockProps = useBlockProps.save({
                className: 'ekwa-faq-answer'
            });

            return (
                <div {...blockProps} itemScope itemProp="acceptedAnswer" itemType="https://schema.org/Answer">
                    <div itemProp="text">
                        <InnerBlocks.Content />
                    </div>
                </div>
            );
        },

        // Add deprecation for FAQ Answer Block
        deprecated: [
            {
                save: () => {
                    // Previous version might not have used useBlockProps
                    return (
                        <div className="ekwa-faq-answer" itemScope itemProp="acceptedAnswer" itemType="https://schema.org/Answer">
                            <div itemProp="text">
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            },
            {
                // Even older version without schema
                save: () => {
                    return (
                        <div className="ekwa-faq-answer">
                            <div>
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            },
        ],
    });
}