import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
    RichText
} from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl
} from '@wordpress/components';

/**
 * Register FAQ Question block (child block for FAQ Section)
 */
export default function registerFaqQuestionBlock() {
    registerBlockType('ekwa/faq-question', {
        title: __('FAQ Question'),
        icon: 'editor-help',
        category: 'design',
        description: __('A question block for use within an FAQ section.'),
        parent: ['ekwa/faq-section', 'core/column', 'core/group', 'core/row'], // Allow in FAQ Section, columns, groups, and rows

        attributes: {
            content: {
                type: 'string',
                default: ''
            },
            level: {
                type: 'number',
                default: 2 // Default to h2
            }
        },

        edit: ({ attributes, setAttributes }) => {
            const { content, level } = attributes;
            const blockProps = useBlockProps({
                className: 'ekwa-faq-question'
            });

            const HeadingTag = 'h' + level;

            return (
                <>
                    <InspectorControls>
                        <PanelBody title={__('Question Settings')}>
                            <SelectControl
                                label={__('Heading Level')}
                                value={level}
                                options={[
                                    { label: 'Heading 2', value: 2 },
                                    { label: 'Heading 3', value: 3 },
                                    { label: 'Heading 4', value: 4 }
                                ]}
                                onChange={(newLevel) => setAttributes({ level: parseInt(newLevel) })}
                            />
                        </PanelBody>
                    </InspectorControls>

                    <div {...blockProps}>
                        <RichText
                            tagName={HeadingTag}
                            value={content}
                            onChange={(newContent) => setAttributes({ content: newContent })}
                            placeholder={__('Write your question here...')}
                            className="ekwa-faq-question-text"
                            // Add itemProp to show in editor
                            itemProp="name"
                        />
                    </div>
                </>
            );
        },

        save: ({ attributes }) => {
            const { content, level } = attributes;
            const blockProps = useBlockProps.save({
                className: 'ekwa-faq-question'
            });

            const HeadingTag = 'h' + level;

            return (
                <div {...blockProps} >
                    {/* Add itemProp="name" directly to the heading tag */}
                    <HeadingTag className="ekwa-faq-question-text" itemProp="name">
                        {content}
                    </HeadingTag>
                </div>
            );
        },

        // Add current version to deprecation to maintain backward compatibility
        deprecated: [
            {
                // Most recent version - almost identical to current but added for safety
                attributes: {
                    content: {
                        type: 'string',
                        default: ''
                    },
                    level: {
                        type: 'number',
                        default: 2
                    }
                },

                save: ({ attributes }) => {
                    const { content, level } = attributes;
                    const blockProps = useBlockProps.save({
                        className: 'ekwa-faq-question'
                    });

                    const HeadingTag = 'h' + level;

                    return (
                        <div {...blockProps} itemScope itemProp="mainEntity" itemType="https://schema.org/Question">
                            <HeadingTag className="ekwa-faq-question-text" itemProp="name">
                                {content}
                            </HeadingTag>
                        </div>
                    );
                },
            },
            {
                // Previous version before useBlockProps
                attributes: {
                    content: {
                        type: 'string',
                        default: ''
                    },
                    level: {
                        type: 'number',
                        default: 2
                    }
                },

                save: ({ attributes }) => {
                    const { content, level } = attributes;
                    const HeadingTag = 'h' + level;

                    return (
                        <div className="ekwa-faq-question" itemScope itemProp="mainEntity" itemType="https://schema.org/Question">
                            <HeadingTag className="ekwa-faq-question-text" itemProp="name">
                                {content}
                            </HeadingTag>
                        </div>
                    );
                },
            },
            {
                // Even older version without schema support
                attributes: {
                    content: {
                        type: 'string',
                        default: ''
                    },
                    level: {
                        type: 'number',
                        default: 2
                    }
                },

                save: ({ attributes }) => {
                    const { content, level } = attributes;
                    const HeadingTag = 'h' + level;

                    return (
                        <div className="ekwa-faq-question">
                            <HeadingTag className="ekwa-faq-question-text">
                                {content}
                            </HeadingTag>
                        </div>
                    );
                },
            },
        ],
    });
}