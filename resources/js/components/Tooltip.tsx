import React, { useState } from 'react';

interface TooltipProps {
  content: React.ReactNode;
  children: React.ReactNode;
  position?: 'top' | 'bottom' | 'left' | 'right';
  delay?: number;
}

const Tooltip = ({
  content,
  children,
  position = 'top',
  delay = 300
}: TooltipProps) => {
  const [isVisible, setIsVisible] = useState(false);
  const [timeoutId, setTimeoutId] = useState<ReturnType<typeof setTimeout> | null>(null);

  const showTooltip = () => {
    if (timeoutId) clearTimeout(timeoutId); // cancel previous timers
    const timeout = setTimeout(() => {
      setIsVisible(true);
    }, delay);
    setTimeoutId(timeout);
  };

  const hideTooltip = () => {
    if (timeoutId) {
      clearTimeout(timeoutId); // cancel any scheduled show
      setTimeoutId(null);
    }
    setIsVisible(false); // hide instantly
  };

  const positionStyles = {
    top: 'bottom-full left-1/2 transform -translate-x-1/2 -translate-y-1 mb-1',
    bottom: 'top-full left-1/2 transform -translate-x-1/2 translate-y-1 mt-1',
    left: 'right-full top-1/2 transform -translate-x-1 -translate-y-1/2 mr-1',
    right: 'left-full top-1/2 transform translate-x-1 -translate-y-1/2 ml-1'
  };

  return (
    <div className="relative inline-block">
      <div
        onMouseEnter={showTooltip}
        onMouseLeave={hideTooltip}
        onFocus={showTooltip}
        onBlur={hideTooltip}
      >
        {children}
      </div>

      {isVisible && (
        <div
          className={`absolute z-50 px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded shadow-sm whitespace-nowrap ${positionStyles[position]}`}
          role="tooltip"
        >
          {content}
          <div
            className={`absolute w-2 h-2 bg-gray-800 transform rotate-45 ${
              position === 'top'
                ? 'top-full -translate-y-1/2 left-1/2 -translate-x-1/2'
                : position === 'bottom'
                ? 'bottom-full translate-y-1/2 left-1/2 -translate-x-1/2'
                : position === 'left'
                ? 'left-full -translate-x-1/2 top-1/2 -translate-y-1/2'
                : 'right-full translate-x-1/2 top-1/2 -translate-y-1/2'
            }`}
          />
        </div>
      )}
    </div>
  );
};

export default Tooltip;
