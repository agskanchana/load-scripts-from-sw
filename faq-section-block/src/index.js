import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    InnerBlocks,
    useBlockProps,
    InspectorControls,
    RichText
} from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl
} from '@wordpress/components';

/**
 * Register a simple container block
 */
registerBlockType('ekwa/container-block', {
    title: __('Container Block'),
    icon: 'layout',
    category: 'design',
    description: __('A simple container block that can hold other blocks.'),

    attributes: {
        blockId: {
            type: 'string',
            default: ''
        }
    },

    edit: ({ attributes, setAttributes }) => {
        const { blockId } = attributes;
        const blockProps = useBlockProps({
            className: 'ekwa-container-block'
        });

        // Generate a unique ID for the block if none exists
        if (!blockId) {
            setAttributes({ blockId: 'container-' + Date.now().toString(36) });
        }

        return (
            <div {...blockProps}>
                <div className="ekwa-container-content">
                    <InnerBlocks />
                </div>
            </div>
        );
    },

    save: ({ attributes }) => {
        const { blockId } = attributes;
        const blockProps = useBlockProps.save({
            className: 'ekwa-container-block',
            id: blockId
        });

        return (
            <div {...blockProps}>
                <div className="ekwa-container-content">
                    <InnerBlocks.Content />
                </div>
            </div>
        );
    },
});

/**
 * Register FAQ Section block
 */
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
            id: blockId
        });

        return (
            <div {...blockProps}>
                <div className="ekwa-faq-content" itemScope itemType="https://schema.org/FAQPage">
                    <InnerBlocks.Content />
                </div>
            </div>
        );
    },
});

/**
 * Register FAQ Question block (child block for FAQ Section)
 */
registerBlockType('ekwa/faq-question', {
    title: __('FAQ Question'),
    icon: 'editor-help',
    category: 'design',
    description: __('A question block for use within an FAQ section.'),
    parent: ['ekwa/faq-section'], // Specifies this as a child block

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
            <div {...blockProps} itemScope itemProp="mainEntity" itemType="https://schema.org/Question">
                <HeadingTag className="ekwa-faq-question-text" itemProp="name">
                    {content}
                </HeadingTag>
            </div>
        );
    }
});

/**
 * Register FAQ Answer block (companion to FAQ Question)
 */
registerBlockType('ekwa/faq-answer', {
    title: __('FAQ Answer'),
    icon: 'editor-textcolor',
    category: 'design',
    description: __('An answer block for use after a FAQ question.'),
    parent: ['ekwa/faq-section'], // Specifies this as a child block

    edit: () => {
        const blockProps = useBlockProps({
            className: 'ekwa-faq-answer'
        });

        return (
            <div {...blockProps}>
                <InnerBlocks
                    template={[['core/paragraph', { placeholder: 'Write your answer here...' }]]}
                    templateLock={false}
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
    }
});