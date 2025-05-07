/**
 * FAQ Block Collection - Main Entry Point
 *
 * Registers all blocks for the FAQ section
 */

// Import block registration functions
import registerContainerBlock from './blocks/container-block';
import registerFaqSectionBlock from './blocks/faq-section';
import registerFaqQuestionBlock from './blocks/faq-question';
import registerFaqAnswerBlock from './blocks/faq-answer';

// Import frontend functionality
// import './frontend';

// Register all blocks
registerContainerBlock();
registerFaqSectionBlock();
registerFaqQuestionBlock();
registerFaqAnswerBlock();