import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
    RichText,
    BlockControls,
    AlignmentToolbar
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
            },
            // Add new alignment attribute with default value
            textAlign: {
                type: 'string',
                default: '' // Empty string means default alignment (inherit)
            }
        },

        edit: ({ attributes, setAttributes }) => {
            const { content, level, textAlign } = attributes;

            // Add textAlign to block props if it's set
            const blockProps = useBlockProps({
                className: 'ekwa-faq-question',
                style: textAlign ? { textAlign } : undefined
            });

            const HeadingTag = 'h' + level;

            return (
                <>
                    {/* Add BlockControls with AlignmentToolbar */}
                    <BlockControls>
                        <AlignmentToolbar
                            value={textAlign}
                            onChange={(newAlign) => setAttributes({ textAlign: newAlign })}
                        />
                    </BlockControls>

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

                            {/* Add text alignment control in sidebar too */}
                            <SelectControl
                                label={__('Text Alignment')}
                                value={textAlign}
                                options={[
                                    { label: 'Default', value: '' },
                                    { label: 'Left', value: 'left' },
                                    { label: 'Center', value: 'center' },
                                    { label: 'Right', value: 'right' }
                                ]}
                                onChange={(newAlign) => setAttributes({ textAlign: newAlign })}
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
                            itemProp="name"
                        />
                    </div>
                </>
            );
        },

        save: ({ attributes }) => {
            const { content, level, textAlign } = attributes;

            // Add textAlign to block props if it's set
            const blockProps = useBlockProps.save({
                className: 'ekwa-faq-question',
                style: textAlign ? { textAlign } : undefined
            });

            const HeadingTag = 'h' + level;

            return (
                <div {...blockProps}>
                    <HeadingTag
                        className="ekwa-faq-question-text"
                        itemProp="name"
                    >
                        {content}
                    </HeadingTag>
                </div>
            );
        },

        // Update the deprecation for backward compatibility
        deprecated: [
            {
                // Current version without text alignment
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

                // This migrate function will set empty textAlign for old blocks
                migrate: (attributes) => {
                    return {
                        ...attributes,
                        textAlign: ''
                    };
                },

                save: ({ attributes }) => {
                    const { content, level } = attributes;
                    const blockProps = useBlockProps.save({
                        className: 'ekwa-faq-question'
                    });

                    const HeadingTag = 'h' + level;

                    return (
                        <div {...blockProps}>
                            <HeadingTag className="ekwa-faq-question-text" itemProp="name">
                                {content}
                            </HeadingTag>
                        </div>
                    );
                },
            },
            {
                // Version with schema attributes on the div
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

                // This migrate function will set empty textAlign for old blocks
                migrate: (attributes) => {
                    return {
                        ...attributes,
                        textAlign: ''
                    };
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

                // This migrate function will set empty textAlign for old blocks
                migrate: (attributes) => {
                    return {
                        ...attributes,
                        textAlign: ''
                    };
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

                // This migrate function will set empty textAlign for old blocks
                migrate: (attributes) => {
                    return {
                        ...attributes,
                        textAlign: ''
                    };
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